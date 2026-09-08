<div class="max-w-[75rem] mx-auto px-4 md:px-6 py-8">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant font-medium mb-6">
        <a class="hover:text-primary transition-colors" href="{{ route('home') }}">{{ __('Beranda') }}</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a class="hover:text-primary transition-colors" href="{{ route('announcements.index') }}">{{ __('Pengumuman') }}</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-bold truncate max-w-xs">{{ $announcement->getTranslation('title', app()->getLocale()) }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Main Article Body (Span 8) -->
        <article class="lg:col-span-8 bg-surface-card rounded-3xl p-6 md:p-10 shadow-sm border border-outline-variant/30">
            <!-- Category & Date Header -->
            <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-outline-variant/20 mb-6">
                <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold">
                    {{ __('Pengumuman Resmi') }}
                </span>
                <div class="flex items-center gap-3 text-xs text-on-surface-variant">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                        <span>{{ $announcement->published_at ? $announcement->published_at->format('d F Y') : now()->format('d F Y') }}</span>
                    </span>
                    <span>•</span>
                    <span>Klinik Ayo Sehat</span>
                </div>
            </div>

            <!-- Title -->
            <h1 class="font-heading text-2xl md:text-3xl lg:text-4xl font-bold text-on-surface leading-tight mb-6">
                {{ $announcement->getTranslation('title', app()->getLocale()) }}
            </h1>

            <!-- Summary Lead -->
            @if($summary = $announcement->getTranslation('summary', app()->getLocale()))
                <div class="p-4 rounded-2xl bg-surface-container-low border-l-4 border-primary text-xs md:text-sm text-on-surface font-medium leading-relaxed mb-6">
                    {{ $summary }}
                </div>
            @endif

            <!-- Image if available -->
            @if($announcement->image)
                <div class="rounded-2xl overflow-hidden mb-6 border border-outline-variant/20 max-h-96">
                    <img src="{{ asset('storage/' . $announcement->image) }}" alt="{{ $announcement->title }}" class="w-full h-full object-cover"/>
                </div>
            @endif

            <!-- Content Body -->
            <div class="prose prose-sm max-w-none text-on-surface leading-relaxed space-y-4 text-xs md:text-sm">
                {!! nl2br(e($announcement->getTranslation('content', app()->getLocale()))) !!}
            </div>

            <!-- Share & Footer Actions -->
            <div class="mt-10 pt-6 border-t border-outline-variant/20 flex flex-wrap items-center justify-between gap-4">
                <a href="{{ route('announcements.index') }}" class="px-5 py-2.5 rounded-xl bg-surface-container text-xs font-bold text-on-surface hover:bg-surface-container-high transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>{{ __('Kembali ke Semua Pengumuman') }}</span>
                </a>

                <div class="flex items-center gap-2">
                    <span class="text-xs text-on-surface-variant font-medium">{{ __('Bagikan:') }}</span>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($announcement->getTranslation('title', app()->getLocale()) . ' ' . url()->current()) }}" target="_blank" class="p-2 rounded-xl bg-status-available/10 text-status-available hover:bg-status-available/20 transition-colors" title="{{ __('Bagikan ke WhatsApp') }}">
                        <span class="material-symbols-outlined text-[18px]">share</span>
                    </a>
                </div>
            </div>
        </article>

        <!-- Right Side: Related Announcements & Booking CTA (Span 4) -->
        <aside class="lg:col-span-4 space-y-6">
            <!-- Related Announcements -->
            <div class="bg-surface-card rounded-2xl p-6 shadow-sm border border-outline-variant/30">
                <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-primary mb-4">{{ __('Pengumuman Lainnya') }}</h3>
                <div class="space-y-4">
                    @foreach($relatedAnnouncements as $rel)
                        <div class="pb-3 border-b border-outline-variant/15 last:border-0 last:pb-0">
                            <span class="text-[10px] text-on-surface-variant block mb-1">
                                {{ $rel->published_at ? $rel->published_at->format('d M Y') : '' }}
                            </span>
                            <h4 class="font-heading text-xs font-bold text-on-surface hover:text-primary transition-colors line-clamp-2">
                                <a href="{{ route('announcements.show', $rel->slug) }}">
                                    {{ $rel->getTranslation('title', app()->getLocale()) }}
                                </a>
                            </h4>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Booking CTA Widget -->
            <div class="bg-gradient-to-br from-primary to-brand-navy rounded-2xl p-6 text-white shadow-md">
                <span class="material-symbols-outlined text-brand-gold text-[32px] mb-2">event_available</span>
                <h3 class="font-heading text-base font-bold">{{ __('Konsultasi dengan Dokter Spesialis?') }}</h3>
                <p class="text-xs text-surface-container-high mt-1 mb-4 leading-relaxed">
                    {{ __('Dapatkan pelayanan rawat jalan terbaik tanpa antre dengan melakukan reservasi jadwal online.') }}
                </p>
                <a href="{{ route('booking.index') }}" class="w-full py-2.5 rounded-xl bg-brand-gold text-brand-navy text-xs font-bold hover:bg-yellow-400 transition-colors flex items-center justify-center gap-1.5 shadow">
                    <span>{{ __('Buat Janji Temu Sekarang') }}</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>
        </aside>

    </div>
</div>
