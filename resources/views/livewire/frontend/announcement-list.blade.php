<div class="max-w-[75rem] mx-auto px-4 md:px-6 py-8">
    <!-- Hero Banner -->
    <section class="bg-brand-navy rounded-3xl p-8 md:p-12 text-surface mb-10 relative overflow-hidden shadow-xl">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                <defs>
                    <pattern height="10" id="ann-grid" patternUnits="userSpaceOnUse" width="10">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"></path>
                    </pattern>
                </defs>
                <rect fill="url(#ann-grid)" height="100" width="100"></rect>
            </svg>
        </div>

        <div class="relative z-10 max-w-3xl">
            <nav class="flex items-center gap-1.5 text-xs text-surface-container-high font-medium mb-3">
                <a class="hover:text-secondary-fixed transition-colors" href="{{ route('home') }}">{{ __('Home') }}</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-secondary-fixed">{{ __('Information & Announcements') }}</span>
            </nav>

            <h1 class="font-heading text-3xl md:text-4xl font-bold tracking-tight text-surface mb-3">
                {{ __('Official Information & Announcement Center') }}
            </h1>
            <p class="text-sm text-surface-container-high leading-relaxed mb-6">
                {{ __('Get the latest information regarding holiday operational schedules, immunization programs, community health education, and clinic system updates.') }}
            </p>

            <!-- Search Bar -->
            <div class="max-w-md relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('Search announcements, topics, or keywords...') }}" class="w-full pl-11 pr-4 py-3 rounded-xl bg-surface text-on-surface text-xs focus:ring-2 focus:ring-primary focus:outline-none shadow-sm"/>
            </div>
        </div>
    </section>

    <!-- Content Grid (8 Cols Articles / 4 Cols Widgets) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Announcements List (Span 8) -->
        <div class="lg:col-span-8 space-y-6">

            @if($announcements->isEmpty())
                <div class="bg-surface-card rounded-2xl p-12 text-center border border-outline-variant/30">
                    <span class="material-symbols-outlined text-outline text-[48px] mb-3">newspaper</span>
                    <h3 class="font-heading text-lg font-bold text-on-surface mb-1">{{ __('No Announcements Found') }}</h3>
                    <p class="text-xs text-on-surface-variant">{{ __('Please try other search keywords.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach($announcements as $ann)
                        @php
                            $title = $ann->getTranslation('title', app()->getLocale());
                            $summary = $ann->getTranslation('summary', app()->getLocale());
                        @endphp
                        <article class="bg-surface-card rounded-2xl p-6 shadow-sm hover:shadow-md border border-outline-variant/30 transition-all flex flex-col justify-between group">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-[10px] font-bold">
                                        {{ __('Official Announcement') }}
                                    </span>
                                    <span class="text-[11px] text-on-surface-variant flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                        <span>{{ $ann->published_at ? $ann->published_at->format('d M Y') : now()->format('d M Y') }}</span>
                                    </span>
                                </div>

                                <h3 class="font-heading text-base font-bold text-on-surface group-hover:text-primary transition-colors line-clamp-2 mb-2">
                                    <a href="{{ route('announcements.show', $ann->slug) }}">
                                        {{ $title }}
                                    </a>
                                </h3>

                                <p class="text-xs text-on-surface-variant line-clamp-3 leading-relaxed">
                                    {{ $summary ?: Str::limit(strip_tags($ann->getTranslation('content', app()->getLocale())), 120) }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-outline-variant/20 flex items-center justify-between">
                                <a href="{{ route('announcements.show', $ann->slug) }}" class="text-xs font-bold text-primary flex items-center gap-1 group-hover:underline">
                                    <span>{{ __('Read More') }}</span>
                                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                </a>
                                <span class="text-[10px] text-outline">KARS Paripurna</span>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pt-4">
                    {{ $announcements->links() }}
                </div>
            @endif

        </div>

        <!-- Right Side: Quick Portal Links (Span 4) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-surface-card rounded-2xl p-6 shadow-sm border border-outline-variant/30">
                <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-primary mb-4">{{ __('Fast Patient Services') }}</h3>
                <div class="space-y-3">
                    <a href="{{ route('doctors.index') }}" class="p-3 rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center gap-3 transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-on-surface block">{{ __('Specialist Doctor Schedules') }}</span>
                            <span class="text-[11px] text-on-surface-variant">{{ __('Check today quota availability') }}</span>
                        </div>
                    </a>

                    <a href="{{ route('booking.index') }}" class="p-3 rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center gap-3 transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">event_available</span>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-on-surface block">{{ __('Book Appointment') }}</span>
                            <span class="text-[11px] text-on-surface-variant">{{ __('New & existing patient registration') }}</span>
                        </div>
                    </a>

                    <a href="{{ route('queue.index') }}" class="p-3 rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center gap-3 transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-status-available/10 text-status-available flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-on-surface block">{{ __('Live Queue Status') }}</span>
                            <span class="text-[11px] text-on-surface-variant">{{ __('Monitor current called numbers') }}</span>
                        </div>
                    </a>

                    <a href="{{ route('checkin.index') }}" class="p-3 rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center gap-3 transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-brand-gold/15 text-brand-navy flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">touch_app</span>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-on-surface block">{{ __('Self Check-in Station') }}</span>
                            <span class="text-[11px] text-on-surface-variant">{{ __('Print physical queue ticket in lobby') }}</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Hotline Card -->
            <div class="bg-brand-navy rounded-2xl p-6 text-white shadow-md">
                <div class="flex items-center gap-2 text-brand-gold text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="material-symbols-outlined text-[20px]">emergency</span>
                    <span>{{ __('Emergency Hotline') }}</span>
                </div>
                <div class="font-heading text-xl font-black text-white mb-2">
                    +62 274 555-999
                </div>
                <p class="text-xs text-surface-container-high leading-relaxed">
                    {{ __('Emergency Room (ER) and rapid response ambulance team on standby 24 hours every day.') }}
                </p>
            </div>
        </div>

    </div>
</div>
