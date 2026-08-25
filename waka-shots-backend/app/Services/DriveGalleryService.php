<?php

namespace App\Services;

use App\Exceptions\DriveConnectionException;
use App\Models\GoogleDriveConnection;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
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
