@if ($connection?->refresh_token)
    <p class="text-sm text-gray-600 dark:text-gray-400">
        Connected as {{ $connection->connected_email ?: 'authorized Google account' }}.
    </p>
@else
    <p class="text-sm text-gray-600 dark:text-gray-400">
        Google Drive is not connected. Connect it to load images from private galleries.
    </p>
@endif
