{{-- Floating WhatsApp button, shared by the public and client-gallery layouts.
     Pass a context-specific pre-filled $message; renders nothing when no
     studio number is set in Studio Settings. --}}
@if($whatsappLink = $siteSetting?->whatsappLink($message ?? null))
    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="whatsapp-float" aria-label="Message us on WhatsApp" title="Message us on WhatsApp" data-cursor="Chat">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3.5 20.5l1.3-4.2A8.5 8.5 0 1 1 8 19.4z"/><path d="M9 8.6c.2-.5.5-.6.8-.6h.5c.2 0 .4.1.5.4l.7 1.6c.1.2 0 .5-.1.6l-.5.6c-.1.2-.1.4 0 .5.6 1.1 1.5 2 2.6 2.6.2.1.4.1.5 0l.6-.5c.2-.1.4-.2.6-.1l1.6.7c.3.1.4.3.4.5v.5c0 .3-.1.6-.6.8-.6.3-1.7.5-3.3-.3-1.5-.8-2.9-2.2-3.7-3.7-.8-1.6-.6-2.7-.3-3.3z"/></svg>
    </a>
@endif
