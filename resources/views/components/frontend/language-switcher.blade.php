@php
    $currentLocale = app()->getLocale();
@endphp

<div class="relative inline-flex items-center" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open" type="button" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors border border-outline-variant/40" aria-label="Change Language">
        <span class="text-base">{{ $currentLocale === 'id' ? '🇮🇩' : '🇬🇧' }}</span>
        <span class="font-bold uppercase tracking-wider text-xs">{{ $currentLocale }}</span>
        <span class="material-symbols-outlined text-[16px] text-outline transition-transform duration-200" :class="{ 'rotate-180': open }">expand_more</span>
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 top-full mt-1.5 w-36 bg-surface-card rounded-xl shadow-xl border border-outline-variant/30 py-1.5 z-50 overflow-hidden"
         style="display: none;">
        <a href="{{ route('locale.switch', 'id') }}" class="flex items-center justify-between px-3 py-2 text-xs font-medium text-on-surface hover:bg-surface-container transition-colors {{ $currentLocale === 'id' ? 'bg-primary/10 text-primary font-bold' : '' }}">
            <span class="flex items-center gap-2">
                <span class="text-sm">🇮🇩</span>
                <span>Indonesia</span>
            </span>
            @if($currentLocale === 'id')
                <span class="material-symbols-outlined text-primary text-[16px]">check</span>
            @endif
        </a>
        <a href="{{ route('locale.switch', 'en') }}" class="flex items-center justify-between px-3 py-2 text-xs font-medium text-on-surface hover:bg-surface-container transition-colors {{ $currentLocale === 'en' ? 'bg-primary/10 text-primary font-bold' : '' }}">
            <span class="flex items-center gap-2">
                <span class="text-sm">🇬🇧</span>
                <span>English</span>
            </span>
            @if($currentLocale === 'en')
                <span class="material-symbols-outlined text-primary text-[16px]">check</span>
            @endif
        </a>
    </div>
</div>
