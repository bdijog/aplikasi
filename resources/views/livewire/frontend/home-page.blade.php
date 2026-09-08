<div>
    <!-- Hero Section -->
    <section class="relative w-full bg-brand-navy overflow-hidden text-surface pt-6 pb-16">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                <defs>
                    <pattern height="10" id="home-grid" patternUnits="userSpaceOnUse" width="10">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"></path>
                    </pattern>
                </defs>
                <rect fill="url(#home-grid)" height="100" width="100"></rect>
            </svg>
        </div>

        <div class="max-w-[75rem] mx-auto px-4 md:px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Hero Left: Headline & Actions -->
                <div class="lg:col-span-7 flex flex-col items-start">
                    <!-- Trust Pill -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-surface-container-lowest/10 backdrop-blur-md border border-white/10 text-xs font-semibold text-brand-gold mb-6 shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                        <span>{{ __('KARS Paripurna Accredited Healthcare Clinic') }}</span>
                    </div>

                    <h1 class="font-heading text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-surface leading-[1.15] mb-4">
                        {{ __('Integrated, Efficient & Family-Friendly Healthcare') }}
                    </h1>

                    <p class="text-sm md:text-base text-surface-container-high leading-relaxed max-w-xl mb-8">
                        {{ __('Register for clinic visits without standing in line. Access updated specialist schedules, book appointments online, and track live queue calls from your smartphone.') }}
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto mb-8">
                        <a href="{{ route('booking.index') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-primary hover:bg-primary-container text-white font-heading text-sm font-bold shadow-lg flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-[20px]">event_available</span>
                            <span>{{ __('Register & Book Appointment') }}</span>
                        </a>

                        <a href="{{ route('doctors.index') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-surface-container-lowest/10 hover:bg-surface-container-lowest/20 text-white font-heading text-sm font-bold border border-white/20 flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                            <span>{{ __('View Doctor Schedules') }}</span>
                        </a>
                    </div>

                    <!-- Live Queue Quick Search Box -->
                    <div class="w-full max-w-lg bg-surface-card rounded-2xl p-3 shadow-xl border border-outline-variant/30 text-on-surface">
                        <form wire:submit="checkQuickTicket" class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
                                <input wire:model="quickTicketCode" type="text" placeholder="{{ __('Have a ticket? Enter ticket / booking code...') }}" class="w-full pl-10 pr-3 py-2 text-xs bg-transparent focus:outline-none text-on-surface"/>
                            </div>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-brand-navy text-white text-xs font-bold hover:bg-primary transition-colors shrink-0">
                                {{ __('Check Queue') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Hero Right: Visual Image & Floating Cards -->
                <div class="lg:col-span-5 relative flex justify-center">
                    <div class="relative w-full max-w-md">
                        <!-- Background Glow Orb -->
                        <div class="absolute -inset-4 bg-gradient-to-r from-primary/30 to-secondary/30 rounded-3xl blur-2xl -z-10"></div>
                        
                        <!-- Receptionist Image -->
                        <div class="rounded-3xl overflow-hidden shadow-2xl border border-white/10 bg-brand-navy">
                            <img src="{{ asset('images/receptionist.png') }}" alt="{{ __('Front Desk Officer Klinik Ayo Sehat') }}" class="w-full h-auto object-cover transform hover:scale-102 transition-transform duration-500"/>
                        </div>

                        <!-- Floating Live Counter Badge -->
                        <div class="absolute -bottom-6 -left-6 bg-surface-card rounded-2xl p-4 shadow-xl border border-outline-variant/30 flex items-center gap-3 animate-bounce duration-1000 text-on-surface">
                            <div class="w-12 h-12 rounded-xl bg-status-available/15 text-status-available flex items-center justify-center">
                                <span class="material-symbols-outlined text-[26px]">timelapse</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-on-surface-variant block">{{ __('Efficient Wait Time') }}</span>
                                <span class="font-heading text-lg font-black text-primary">&lt; 15 Menit</span>
                            </div>
                        </div>

                        <!-- Floating Rating Badge -->
                        <div class="absolute -top-4 -right-4 bg-surface-card rounded-2xl p-3 shadow-xl border border-outline-variant/30 flex items-center gap-2 text-on-surface">
                            <span class="material-symbols-outlined text-brand-gold text-[22px]">star</span>
                            <div class="text-left">
                                <span class="text-xs font-black block">4.9 / 5.0</span>
                                <span class="text-[9px] text-on-surface-variant">{{ __('Patient Satisfaction') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4 Core Pillars Section -->
    <section class="max-w-[75rem] mx-auto px-4 md:px-6 -mt-8 relative z-20 mb-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Jadwal Dokter -->
            <a href="{{ route('doctors.index') }}" class="group bg-surface-card rounded-2xl p-6 shadow-md hover:shadow-xl border border-outline-variant/30 hover:border-primary/50 transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white flex items-center justify-center mb-4 transition-colors">
                        <span class="material-symbols-outlined text-[26px]">calendar_month</span>
                    </div>
                    <h3 class="font-heading text-base font-bold text-on-surface mb-1 group-hover:text-primary transition-colors">
                        {{ __('Doctor Schedule & Polyclinics') }}
                    </h3>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        {{ __('View specialist doctor schedules and real-time appointment quota availability.') }}
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 flex items-center justify-between text-xs font-bold text-primary">
                    <span>{{ __('View Schedule') }}</span>
                    <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Card 2: Booking Appointment -->
            <a href="{{ route('booking.index') }}" class="group bg-surface-card rounded-2xl p-6 shadow-md hover:shadow-xl border border-outline-variant/30 hover:border-primary/50 transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary group-hover:bg-secondary group-hover:text-white flex items-center justify-center mb-4 transition-colors">
                        <span class="material-symbols-outlined text-[26px]">event_available</span>
                    </div>
                    <h3 class="font-heading text-base font-bold text-on-surface mb-1 group-hover:text-primary transition-colors">
                        {{ __('Book Appointment') }}
                    </h3>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        {{ __('New patient registration & specialist schedule booking in a unified workflow.') }}
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 flex items-center justify-between text-xs font-bold text-secondary">
                    <span>{{ __('Book Online') }}</span>
                    <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Card 3: Status Antrean -->
            <a href="{{ route('queue.index') }}" class="group bg-surface-card rounded-2xl p-6 shadow-md hover:shadow-xl border border-outline-variant/30 hover:border-primary/50 transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-status-available/10 text-status-available group-hover:bg-status-available group-hover:text-white flex items-center justify-center mb-4 transition-colors">
                        <span class="material-symbols-outlined text-[26px]">confirmation_number</span>
                    </div>
                    <h3 class="font-heading text-base font-bold text-on-surface mb-1 group-hover:text-primary transition-colors">
                        {{ __('Live Queue Status') }}
                    </h3>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        {{ __('Monitor polyclinic calling numbers directly with audio notifications.') }}
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 flex items-center justify-between text-xs font-bold text-status-available">
                    <span>{{ __('Monitor Queue') }}</span>
                    <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Card 4: Self Check-in -->
            <a href="{{ route('checkin.index') }}" class="group bg-surface-card rounded-2xl p-6 shadow-md hover:shadow-xl border border-outline-variant/30 hover:border-primary/50 transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-brand-gold/15 text-brand-navy group-hover:bg-brand-gold group-hover:text-brand-navy flex items-center justify-center mb-4 transition-colors">
                        <span class="material-symbols-outlined text-[26px]">touch_app</span>
                    </div>
                    <h3 class="font-heading text-base font-bold text-on-surface mb-1 group-hover:text-primary transition-colors">
                        {{ __('Self Check-in Station') }}
                    </h3>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        {{ __('Confirm arrival attendance on the day of visit and quickly print physical queue tickets.') }}
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 flex items-center justify-between text-xs font-bold text-brand-navy">
                    <span>{{ __('Check-in Now') }}</span>
                    <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </a>
        </div>
    </section>

    <!-- Live Queue Monitor Glance Bar -->
    <section class="max-w-[75rem] mx-auto px-4 md:px-6 mb-16">
        <div class="bg-surface-card rounded-3xl p-6 md:p-8 shadow-sm border border-outline-variant/30">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-status-available animate-ping"></div>
                    <div>
                        <h2 class="font-heading text-lg font-bold text-on-surface">{{ __('Current Live Queue Calls') }}</h2>
                        <span class="text-xs text-on-surface-variant">{{ __('Direct synchronization with polyclinic monitor screens') }}</span>
                    </div>
                </div>

                <a href="{{ route('queue.display') }}" target="_blank" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">tv</span>
                    <span>{{ __('Open TV Monitor') }}</span>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($activeQueues as $q)
                    <div class="p-4 rounded-2xl bg-surface-container-low border border-outline-variant/20 flex flex-col justify-between">
                        <div>
                            <span class="text-[11px] font-bold text-on-surface-variant block mb-1">{{ $q['name'] }}</span>
                            <span class="text-[10px] text-outline">{{ $q['room'] }}</span>
                        </div>
                        <div class="mt-3 pt-2 border-t border-outline-variant/15 flex items-baseline justify-between">
                            <span class="font-heading text-2xl font-black text-primary">{{ $q['number'] }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-status-available/10 text-status-available font-bold">{{ __('Serving') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Doctors Section -->
    <section class="max-w-[75rem] mx-auto px-4 md:px-6 mb-16">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-8">
            <div>
                <span class="text-xs font-bold text-primary uppercase tracking-widest">{{ __('Featured Medical Team') }}</span>
                <h2 class="font-heading text-2xl md:text-3xl font-bold text-on-surface mt-1">
                    {{ __('Experienced Specialist Doctors') }}
                </h2>
            </div>

            <a href="{{ route('doctors.index') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                <span>{{ __('View All Doctors') }}</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredDoctors as $doc)
                <div class="bg-surface-card rounded-2xl p-5 shadow-sm hover:shadow-md border border-outline-variant/30 transition-all flex flex-col justify-between">
                    <div>
                        <div class="w-full h-44 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-heading font-bold text-3xl overflow-hidden mb-4 border border-primary/20">
                            @if($doc->photo)
                                <img class="w-full h-full object-cover" src="{{ asset('storage/' . $doc->photo) }}" alt="{{ $doc->name }}"/>
                            @else
                                {{ strtoupper(substr($doc->name, 0, 2)) }}
                            @endif
                        </div>

                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] bg-primary/10 text-primary font-bold">
                            {{ $doc->getTranslation('specialty', app()->getLocale()) }}
                        </span>

                        <h3 class="font-heading text-base font-bold text-on-surface mt-2 truncate">
                            {{ $doc->name }}
                        </h3>
                        <p class="text-[11px] text-on-surface-variant mt-0.5">STR: {{ $doc->license_number ?? '-' }}</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-outline-variant/20 flex gap-2">
                        <a href="{{ route('booking.index', ['doctor_id' => $doc->id]) }}" class="flex-1 py-2 px-3 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container text-center transition-colors">
                            {{ __('Booking') }}
                        </a>
                        <a href="{{ route('doctors.index', ['search' => $doc->name]) }}" class="py-2 px-3 rounded-xl bg-surface-container text-xs font-bold text-primary hover:bg-surface-container-high transition-colors">
                            {{ __('Schedule') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Latest Announcements & Health Tips Section -->
    <section class="bg-surface-container-low py-16">
        <div class="max-w-[75rem] mx-auto px-4 md:px-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-8">
                <div>
                    <span class="text-xs font-bold text-primary uppercase tracking-widest">{{ __('News & Announcements') }}</span>
                    <h2 class="font-heading text-2xl md:text-3xl font-bold text-on-surface mt-1">
                        {{ __('Latest Updates from Klinik Ayo Sehat') }}
                    </h2>
                </div>

                <a href="{{ route('announcements.index') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                    <span>{{ __('View All Updates') }}</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($latestAnnouncements as $ann)
                    <article class="bg-surface-card rounded-2xl p-6 shadow-sm hover:shadow-md border border-outline-variant/30 transition-all flex flex-col justify-between group">
                        <div>
                            <span class="text-[11px] text-on-surface-variant flex items-center gap-1 mb-2">
                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                <span>{{ $ann->published_at ? $ann->published_at->format('d M Y') : '' }}</span>
                            </span>

                            <h3 class="font-heading text-base font-bold text-on-surface group-hover:text-primary transition-colors line-clamp-2 mb-2">
                                <a href="{{ route('announcements.show', $ann->slug) }}">
                                    {{ $ann->getTranslation('title', app()->getLocale()) }}
                                </a>
                            </h3>

                            <p class="text-xs text-on-surface-variant line-clamp-3 leading-relaxed">
                                {{ $ann->getTranslation('summary', app()->getLocale()) ?: Str::limit(strip_tags($ann->getTranslation('content', app()->getLocale())), 100) }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-outline-variant/20">
                            <a href="{{ route('announcements.show', $ann->slug) }}" class="text-xs font-bold text-primary flex items-center gap-1 group-hover:underline">
                                <span>{{ __('Read More') }}</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Accreditation & Insurance Partners Bar -->
    <section class="max-w-[75rem] mx-auto px-4 md:px-6 py-12 border-t border-outline-variant/20">
        <div class="text-center mb-6">
            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">{{ __('Supported by Official Partners & Nationally Accredited') }}</span>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-8 md:gap-12 opacity-80">
            <div class="flex items-center gap-2 text-sm font-bold text-on-surface">
                <span class="material-symbols-outlined text-primary text-[24px]">verified</span>
                <span>KARS Paripurna</span>
            </div>
            <div class="flex items-center gap-2 text-sm font-bold text-on-surface">
                <span class="material-symbols-outlined text-secondary text-[24px]">health_and_safety</span>
                <span>BPJS Kesehatan</span>
            </div>
            <div class="flex items-center gap-2 text-sm font-bold text-on-surface">
                <span class="material-symbols-outlined text-brand-navy text-[24px]">local_hospital</span>
                <span>Kementerian Kesehatan RI</span>
            </div>
            <div class="flex items-center gap-2 text-sm font-bold text-on-surface">
                <span class="material-symbols-outlined text-brand-gold text-[24px]">security</span>
                <span>ISO 9001:2015</span>
            </div>
        </div>
    </section>
</div>
