<x-filament-widgets::widget>
    <div class="fi-quick-actions-ctn">
        <div class="fi-quick-actions-row">
            @foreach ($actions as $action)
                <a href="{{ $action["url"] }}" class="fi-quick-action-btn">
                    <span class="fi-quick-action-label">{{ $action["label"] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
