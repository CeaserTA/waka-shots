<?php

namespace App\Http\Controllers;

use App\Exceptions\DriveConnectionException;
use App\Models\Gallery;
use App\Models\Testimonial;
use App\Services\DriveGalleryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;
use ZipArchive;

class GalleryController extends Controller
{
    public function __construct(private readonly DriveGalleryService $drive)
    {
    }

    public function show(Request $request, string $token): Response
    {
        $gallery = $this->findAvailableGallery($token);

        if (! $gallery) {
            return response()->view('galleries.unavailable', [], 200);
        }

        try {
            $images = $this->drive->listImagesInFolder($gallery->drive_folder_id);
        } catch (Throwable $exception) {
            Log::error('Unable to list gallery images.', ['gallery_id' => $gallery->id, 'exception' => $exception]);

            return response()->view('galleries.error', [], 503);
        }

        $this->logAccess($request, $gallery, 'view');

        $testimonial = Testimonial::where('gallery_id', $gallery->id)->whereIn('status', ['pending', 'approved'])->first();

        return response()->view('galleries.show', compact('gallery', 'images', 'testimonial'));
    }

    public function submitTestimonial(Request $request, string $token): \Illuminate\Http\RedirectResponse
    {
        $gallery = $this->findAvailableGallery($token);

        if (! $gallery) {
            return redirect()->route('gallery.show', $token)->with('error', 'This gallery is no longer available.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'quote' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $testimonial = Testimonial::where('gallery_id', $gallery->id)->first();

        if ($testimonial && in_array($testimonial->status, ['pending', 'approved'], true)) {
            return redirect()->route('gallery.show', $token)->with('error', 'A review has already been submitted for this gallery.');
        }

        if ($testimonial) {
            $testimonial->update([
                'quote' => $validated['quote'],
                'rating' => $validated['rating'],
                'status' => 'pending',
                'is_featured' => false,
            ]);
        } else {
            Testimonial::create([
                'gallery_id' => $gallery->id,
                'quote' => $validated['quote'],
                'rating' => $validated['rating'],
                'status' => 'pending',
                'is_featured' => false,
            ]);
        }

        return redirect()->route('gallery.show', $token)->with('success', 'Thanks for your review.');
    }
    public function preview(Request $request, string $token, string $imageId): Response
    {
        return $this->serveImage($request, $token, $imageId, false);
    }

    public function download(Request $request, string $token, string $imageId): Response
    {
        return $this->serveImage($request, $token, $imageId, true);
    }

    private function serveImage(Request $request, string $token, string $imageId, bool $asDownload): Response
    {
        $gallery = $this->findAvailableGallery($token);

        if (! $gallery) {
            return response()->view('galleries.unavailable', [], 200);
        }

        try {
            $image = collect($this->drive->listImagesInFolder($gallery->drive_folder_id))
                ->firstWhere('id', $imageId);

            abort_if(! $image, 404);

            $file = $this->drive->downloadFile($imageId);
            if ($asDownload) {
                $this->logAccess($request, $gallery, 'download', $imageId);
            }

            return response($file['contents'], 200, [
                'Content-Type' => $file['mimeType'],
                'Content-Disposition' => ($asDownload ? 'attachment' : 'inline') . '; filename="' . addcslashes($file['name'], '"\\') . '"',
            ]);
        } catch (Throwable $exception) {
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $exception;
            }

            Log::error('Unable to download gallery image.', [
                'gallery_id' => $gallery->id,
                'image_id' => $imageId,
                'exception' => $exception,
            ]);

            return response()->view('galleries.error', [], 503);
        }
    }

    public function downloadAll(Request $request, string $token): \Symfony\Component\HttpFoundation\Response
    {
        $gallery = $this->findAvailableGallery($token);

        if (! $gallery) {
            return response()->view('galleries.unavailable', [], 200);
        }

        $zipPath = null;

        try {
            $images = $this->drive->listImagesInFolder($gallery->drive_folder_id);
            $directory = storage_path('app/temp');
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $zipPath = $directory . DIRECTORY_SEPARATOR . Str::uuid() . '.zip';
            $zip = new ZipArchive();
            abort_unless($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true, 500);

            // Move this to a queued job with client polling if galleries commonly exceed ~100 images.
            foreach ($images as $image) {
                $file = $this->drive->downloadFile($image['id']);
                $zip->addFromString($file['name'] ?: $image['id'], $file['contents']);
            }
            $zip->close();

            $this->logAccess($request, $gallery, 'download_all');

            return response()->download(
                $zipPath,
                $this->safeFilename($gallery->client_name . '-' . $gallery->event_name) . '.zip',
                ['Content-Type' => 'application/zip'],
            )->deleteFileAfterSend(true);
        } catch (Throwable $exception) {
            if ($zipPath && is_file($zipPath)) {
                @unlink($zipPath);
            }

            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $exception;
            }

            Log::error('Unable to create gallery download archive.', ['gallery_id' => $gallery->id, 'exception' => $exception]);

            return response()->view('galleries.error', [], 503);
        }
    }

    private function findAvailableGallery(string $token): ?Gallery
    {
        $gallery = Gallery::where('access_token', $token)->first();

        return $gallery && $gallery->is_active && (! $gallery->expires_at || $gallery->expires_at->isFuture())
            ? $gallery
            : null;
    }

    private function logAccess(Request $request, Gallery $gallery, string $eventType, ?string $imageId = null): void
    {
        $gallery->accessLogs()->create([
            'event_type' => $eventType,
            'image_id' => $imageId,
            'ip_address' => $request->ip() ?? '0.0.0.0',
            'user_agent' => $request->userAgent(),
        ]);
    }

    private function safeFilename(string $filename): string
    {
        return trim((string) preg_replace('/[^A-Za-z0-9._-]+/', '-', $filename), '-_.') ?: 'gallery';
    }
}
