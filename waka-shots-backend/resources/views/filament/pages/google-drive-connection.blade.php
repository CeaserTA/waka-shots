<x-filament-panels::page>
    @php($connection = $this->getConnection())

    <x-filament::section>
        <x-slot name="heading">Google Drive connection</x-slot>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            @if ($connection?->refresh_token)
                Connected as {{ $connection->connected_email ?: 'authorized Google account' }}.
            @else
                Not connected.
            @endif
        </p>
    </x-filament::section>
</x-filament-panels::page>
