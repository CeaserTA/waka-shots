<?php

namespace App\Services;

use App\Exceptions\DriveConnectionException;
use App\Models\GoogleDriveConnection;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Http;
use Throwable;

class DriveGalleryService
{
    public function __construct(private readonly ?Client $client = null)
    {
    }

    public function listImagesInFolder(string $folderId): array
    {
        $drive = new Drive($this->authenticatedClient());
        $response = $drive->files->listFiles([
            'q' => sprintf("'%s' in parents and trashed = false and mimeType contains 'image/'", addslashes($folderId)),
            'spaces' => 'drive',
            'fields' => 'files(id,name,thumbnailLink,webContentLink)',
            'pageSize' => 1000,
        ]);

        return array_map(fn (DriveFile $file): array => [
            'id' => $file->getId(),
            'name' => $file->getName(),
            'thumbnailLink' => $this->enhanceThumbnailLink($file->getThumbnailLink()),
            'webContentLink' => $file->getWebContentLink(),
        ], $response->getFiles());
    }

    public function enhanceThumbnailLink(?string $thumbnailLink): ?string
    {
        if ($thumbnailLink === null) {
            return null;
        }

        return preg_replace('/=s\d+$/', '=s1600', $thumbnailLink) ?? $thumbnailLink;
    }

    /**
     * Fetch a Drive file through the server so its URL and credentials stay private.
     *
     * @return array{contents: string, name: string, mimeType: string}
     */
    public function downloadFile(string $fileId): array
    {
        $drive = new Drive($this->authenticatedClient());
        $file = $drive->files->get($fileId, [
            'fields' => 'name,mimeType',
        ]);
        $response = $drive->files->get($fileId, [
            'alt' => 'media',
        ]);

        return [
            'contents' => $response->getBody()->getContents(),
            'name' => $file->getName() ?: 'photo',
            'mimeType' => $file->getMimeType() ?: 'application/octet-stream',
        ];
    }

    /**
     * Same as downloadFile(), but confirms the file actually lives in the
     * given gallery folder before fetching its bytes — a single metadata
     * call with a parents check, instead of re-listing every file in the
     * folder (previously done by every single caller of this method just to
     * find one already-known file ID) plus a second, separate metadata call.
     * Cuts a click-to-image-shown request from 3 sequential Drive API calls
     * down to 2.
     *
     * @return array{contents: string, name: string, mimeType: string}|null null if the file isn't in this folder (or doesn't exist)
     */
    public function downloadFileInFolder(string $fileId, string $folderId): ?array
    {
        $drive = new Drive($this->authenticatedClient());

        try {
            $file = $drive->files->get($fileId, [
                'fields' => 'name,mimeType,parents,trashed',
            ]);
        } catch (Throwable) {
            return null;
        }

        if ($file->getTrashed() || ! in_array($folderId, $file->getParents() ?? [], true)) {
            return null;
        }

        $response = $drive->files->get($fileId, [
            'alt' => 'media',
        ]);

        return [
            'contents' => $response->getBody()->getContents(),
            'name' => $file->getName() ?: 'photo',
            'mimeType' => $file->getMimeType() ?: 'application/octet-stream',
        ];
    }

    /**
     * Fetch a preview-sized thumbnail through the server, same-origin.
     *
     * thumbnailLink is already a public, pre-signed Google URL — the grid
     * loads it directly with a plain <img>, which never needs CORS. But
     * the lightbox loads images into WebGL textures, and that DOES require
     * CORS — and googleusercontent.com simply never sends the
     * Access-Control-Allow-Origin header, for any origin. That's not
     * something we can configure (unlike our own R2 bucket); the only way
     * a cross-origin image becomes usable as a WebGL texture without CORS
     * support from the host is to not be cross-origin at all. So: one
     * lightweight Drive metadata call to get the link and verify the file
     * belongs to this folder, then a plain HTTP fetch of that (small,
     * public, unauthenticated) thumbnail URL, served back from our own
     * origin. Nowhere near as heavy as downloadFileInFolder()'s full
     * original — no `alt=media` Drive API call at all.
     *
     * @return array{contents: string, mimeType: string}|null null if the file isn't in this folder, or has no thumbnail
     */
    public function fetchThumbnail(string $fileId, string $folderId): ?array
    {
        $drive = new Drive($this->authenticatedClient());

        try {
            $file = $drive->files->get($fileId, [
                'fields' => 'thumbnailLink,parents,trashed',
            ]);
        } catch (Throwable) {
            return null;
        }

        if ($file->getTrashed() || ! in_array($folderId, $file->getParents() ?? [], true)) {
            return null;
        }

        $thumbnailLink = $this->enhanceThumbnailLink($file->getThumbnailLink());

        if (! $thumbnailLink) {
            return null;
        }

        try {
            $response = Http::timeout(6)->get($thumbnailLink);
        } catch (Throwable) {
            // A slow/unreachable connection to Google here shouldn't read as
            // "something broke" (502) the way a real application error
            // should — it's an external, often-transient network condition.
            // Treat it the same as "couldn't get this one" (404) so the rest
            // of the gallery isn't affected and the failure doesn't get
            // logged as loudly as a genuine bug.
            return null;
        }

        if ($response->failed()) {
            return null;
        }

        return [
            'contents' => $response->body(),
            'mimeType' => $response->header('Content-Type') ?: 'image/jpeg',
        ];
    }

    private function authenticatedClient(): Client
    {
        $connection = GoogleDriveConnection::query()->latest('id')->first();

        if (! $connection || blank($connection->refresh_token)) {
            throw new DriveConnectionException('Google Drive is not connected. Authorize Google Drive in the admin settings first.');
        }

        $client = $this->client ?? new Client();
        $client->setClientId((string) config('services.google.client_id'));
        $client->setClientSecret((string) config('services.google.client_secret'));
        $client->setRedirectUri((string) config('services.google.redirect'));
        $client->setScopes([Drive::DRIVE_READONLY]);
        $client->setAccessToken([
            'access_token' => $connection->access_token,
            'expires_in' => $connection->token_expires_at?->diffInSeconds(now()),
        ]);

        if ($connection->token_expires_at?->isPast() || $client->isAccessTokenExpired()) {
            try {
                $token = $client->fetchAccessTokenWithRefreshToken($connection->refresh_token);
            } catch (Throwable $exception) {
                throw new DriveConnectionException(
                    'Google Drive authorization could not be refreshed. Reconnect Google Drive and verify that this server can reach oauth2.googleapis.com.',
                    previous: $exception,
                );
            }

            if (isset($token['error']) || blank($token['access_token'])) {
                throw new DriveConnectionException('Google Drive authorization has expired. Please reconnect Google Drive.');
            }

            $connection->update([
                'access_token' => $token['access_token'],
                'token_expires_at' => isset($token['expires_in'])
                    ? Carbon::now()->addSeconds((int) $token['expires_in'])
                    : null,
            ]);
        }

        return $client;
    }
}
