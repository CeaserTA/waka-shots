{{-- Unlike StatsOverviewWidget, a plain Widget has no polling of its own,
     so this feed only ever updated on a full page refresh. --}}
<x-filament-widgets::widget wire:poll.10s>
    <x-filament::section heading="Recent Activity">
        @if ($activities->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No recent activity</p>
        @else
            <div class="divide-y divide-gray-200 dark:divide-white/10">
                @foreach ($activities as $activity)
                    <a href="{{ $activity['link'] }}" class="flex items-center gap-3 py-3 first:pt-0 last:pb-0 hover:bg-gray-50 dark:hover:bg-white/5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $activity['type'] === 'enquiry' ? 'bg-primary-100 text-primary-600 dark:bg-primary-500/15 dark:text-primary-400' : 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300' }}" aria-hidden="true">
                            @if ($activity['type'] === 'enquiry')
                                <x-heroicon-o-inbox class="h-4 w-4" />
                            @else
                                <x-heroicon-o-photo class="h-4 w-4" />
                            @endif
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $activity['label'] }}</span>
                            <span class="block truncate text-sm text-gray-900 dark:text-white">{{ $activity['description'] }}</span>
                        </span>
                        <time class="shrink-0 text-xs text-gray-500 dark:text-gray-400" datetime="{{ $activity['timestamp']->toIso8601String() }}">{{ $activity['timestamp']->diffForHumans() }}</time>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
