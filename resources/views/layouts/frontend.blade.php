<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('Klinik Ayo Sehat - Portal Antrean & Jadwal Dokter') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-surface font-sans text-on-surface min-h-screen flex flex-col antialiased selection:bg-primary-container selection:text-on-primary-container" x-data="{ mobileMenuOpen: false }">

    <!-- Top Announcement Bar -->
    <header class="fixed top-0 left-0 w-full z-50 bg-surface-container-lowest/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
        <div class="w-full bg-brand-navy text-surface px-4 md:px-6 py-1.5">
            <div class="max-w-[75rem] mx-auto flex items-center justify-between text-xs font-medium">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-brand-gold text-[15px]">verified</span>
                        <span>{{ __('Akreditasi Paripurna KARS & ISO 9001:2015') }}</span>
                    </span>
                    <span class="hidden md:inline text-outline-variant">•</span>
                    <span class="hidden md:flex items-center gap-1">
                        <span class="material-symbols-outlined text-secondary-fixed text-[15px]">schedule</span>
                        <span>{{ __('Layanan IGD 24 Jam & Poliklinik Rawat Jalan') }}</span>
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1.5 bg-error/20 text-on-error px-2.5 py-0.5 rounded-full text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-error animate-pulse"></span>
                        <span>{{ __('Hotline Darurat: +62 274 555-999') }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Navigation Bar -->
        <div class="h-20 max-w-[75rem] mx-auto px-4 md:px-6 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <div class="flex items-center gap-6 shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img alt="Klinik Ayo Sehat Logo" class="h-9 w-auto object-contain transition-transform group-hover:scale-105" src="{{ asset('images/logo.png') }}"/>
                    <div class="flex flex-col">
                        <span class="font-heading font-bold text-lg md:text-xl text-primary tracking-tight leading-tight">Klinik Ayo Sehat</span>
                        <span class="text-xs text-on-surface-variant -mt-0.5 font-medium">HealthQueue Portal Terpadu</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden lg:flex items-center gap-1 text-sm font-medium">
                    <a href="{{ route('doctors.index') }}" class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('doctors.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        {{ __('Jadwal Dokter & Poliklinik') }}
                    </a>
                    <a href="{{ route('booking.index') }}" class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('booking.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        {{ __('Booking Janji Temu') }}
                    </a>
                    <a href="{{ route('queue.index') }}" class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('queue.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        {{ __('Status Antrean') }}
                    </a>
                    <a href="{{ route('checkin.index') }}" class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('checkin.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        {{ __('Self Check-in') }}
                    </a>
                    <a href="{{ route('announcements.index') }}" class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('announcements.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        {{ __('Pengumuman') }}
                    </a>
                </nav>
            </div>

            <!-- Right Controls: Language Switcher, Patient Auth, Mobile Menu Toggle -->
            <div class="flex items-center gap-3 shrink-0">
                <!-- Language Switcher -->
                <x-frontend.language-switcher />

                <!-- TV Queue Monitor Shortcut Link -->
                <a href="{{ route('queue.display') }}" target="_blank" title="{{ __('Buka Layar Monitor Antrean TV') }}" class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-surface-container text-primary font-medium text-xs hover:bg-surface-container-high transition-colors border border-outline-variant/40">
                    <span class="material-symbols-outlined text-[18px]">tv</span>
                    <span>{{ __('Layar TV') }}</span>
                </a>

                <!-- Patient Profile or Login -->
                @if(auth('patient')->check())
                    <div class="relative" x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false">
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 pl-2 py-1 pr-3 rounded-full bg-surface-container-low hover:bg-surface-container border border-outline-variant/30 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-primary/20 text-primary font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr(auth('patient')->user()->name, 0, 2)) }}
                            </div>
                            <div class="hidden sm:flex flex-col text-left">
                                <span class="text-xs font-semibold text-on-surface truncate max-w-[120px]">{{ auth('patient')->user()->name }}</span>
                                <span class="text-[10px] text-status-available font-medium">{{ __('RM: ') }}{{ auth('patient')->user()->medical_record_number ?? '-' }}</span>
                            </div>
                            <span class="material-symbols-outlined text-[16px] text-outline">expand_more</span>
                        </button>
                        <div x-show="userMenuOpen" x-cloak class="absolute right-0 top-full mt-2 w-48 bg-surface-card rounded-xl shadow-xl border border-outline-variant/30 py-2 z-50">
                            <div class="px-4 py-2 border-b border-outline-variant/20">
                                <p class="text-xs font-bold text-on-surface">{{ auth('patient')->user()->name }}</p>
                                <p class="text-[11px] text-on-surface-variant truncate">{{ auth('patient')->user()->phone ?? auth('patient')->user()->email }}</p>
                            </div>
                            <a href="{{ route('queue.index') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-on-surface hover:bg-surface-container">
                                <span class="material-symbols-outlined text-[18px] text-primary">confirmation_number</span>
                                <span>{{ __('Tiket Antrean Saya') }}</span>
                            </a>
                            <form method="POST" action="{{ route('patient.logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-xs text-error hover:bg-error-container/20 text-left">
                                    <span class="material-symbols-outlined text-[18px]">logout</span>
                                    <span>{{ __('Keluar (Logout)') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('booking.index') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white font-semibold text-xs shadow hover:bg-primary-container transition-all">
                        <span class="material-symbols-outlined text-[18px]">event</span>
                        <span>{{ __('Daftar & Booking') }}</span>
                    </a>
                @endif

                <!-- Mobile Hamburger Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="lg:hidden p-2 rounded-lg text-on-surface hover:bg-surface-container" aria-label="Toggle Menu">
                    <span class="material-symbols-outlined text-[24px]" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenuOpen" x-cloak x-transition class="lg:hidden bg-surface-card border-b border-outline-variant/30 px-4 py-4 space-y-2 shadow-lg">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('home') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface hover:bg-surface-container' }}">
                {{ __('Beranda') }}
            </a>
            <a href="{{ route('doctors.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('doctors.*') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface hover:bg-surface-container' }}">
                {{ __('Jadwal Dokter & Poliklinik') }}
            </a>
            <a href="{{ route('booking.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('booking.*') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface hover:bg-surface-container' }}">
                {{ __('Booking Janji Temu') }}
            </a>
            <a href="{{ route('queue.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('queue.*') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface hover:bg-surface-container' }}">
                {{ __('Status Antrean') }}
            </a>
            <a href="{{ route('checkin.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('checkin.*') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface hover:bg-surface-container' }}">
                {{ __('Self Check-in') }}
            </a>
            <a href="{{ route('announcements.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('announcements.*') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface hover:bg-surface-container' }}">
                {{ __('Pengumuman') }}
            </a>
            <a href="{{ route('queue.display') }}" target="_blank" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-primary hover:bg-surface-container">
                <span class="material-symbols-outlined text-[18px]">tv</span>
                <span>{{ __('Buka Layar Monitor TV') }}</span>
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="w-full pt-28 flex-1 bg-surface">
        @if (session('success'))
            <div class="max-w-[75rem] mx-auto px-4 md:px-6 pt-4">
                <div class="p-4 rounded-xl bg-status-available/10 border border-status-available/30 text-status-available flex items-center gap-3">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-[75rem] mx-auto px-4 md:px-6 pt-4">
                <div class="p-4 rounded-xl bg-error/10 border border-error/30 text-error flex items-center gap-3">
                    <span class="material-symbols-outlined">error</span>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="w-full bg-brand-navy text-inverse-on-surface mt-auto">
        <div class="max-w-[75rem] mx-auto px-4 md:px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
                <!-- Col 1-2: Brand & Contact -->
                <div class="lg:col-span-2 flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-1.5 rounded-lg bg-surface-container-lowest">
                            <img alt="Klinik Ayo Sehat Logo" class="h-7 w-auto object-contain" src="{{ asset('images/logo.png') }}"/>
                        </div>
                        <span class="font-heading text-xl text-surface font-bold">Klinik Ayo Sehat</span>
                    </div>
                    <p class="text-sm text-surface-container-high leading-relaxed pr-6">
                        {{ __('Pusat pelayanan kesehatan rawat jalan terpadu modern berstandar akreditasi nasional. Mengedepankan efisiensi antrean presisi, keramahan medis, dan transparansi waktu layanan untuk seluruh keluarga.') }}
                    </p>
                    <div class="flex flex-col gap-2.5 mt-2">
                        <div class="flex items-center gap-3 text-surface-container-high text-xs">
                            <span class="material-symbols-outlined text-secondary-fixed text-[18px]">location_on</span>
                            <span>{{ __('Jl. Sehat Utama No. 88, Kotabaru, Yogyakarta 55224') }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-surface-container-high text-xs">
                            <span class="material-symbols-outlined text-brand-gold text-[18px]">call</span>
                            <span>Call Center: (0274) 555-123 | IGD: +62 274 555-999</span>
                        </div>
                        <div class="flex items-center gap-3 text-surface-container-high text-xs">
                            <span class="material-symbols-outlined text-secondary-fixed text-[18px]">mail</span>
                            <span>layananpasien@klinikayosehat.id</span>
                        </div>
                    </div>
                </div>

                <!-- Col 3: Polyclinics -->
                <div class="flex flex-col gap-3">
                    <span class="font-heading text-base text-surface font-semibold">{{ __('Layanan Poliklinik') }}</span>
                    <div class="flex flex-col gap-2 text-xs text-surface-container-high">
                        <a class="hover:text-secondary-fixed transition-colors" href="{{ route('doctors.index') }}">{{ __('Poli Penyakit Dalam') }}</a>
                        <a class="hover:text-secondary-fixed transition-colors" href="{{ route('doctors.index') }}">{{ __('Poli Anak & Tumbuh Kembang') }}</a>
                        <a class="hover:text-secondary-fixed transition-colors" href="{{ route('doctors.index') }}">{{ __('Poli Gigi & Bedah Mulut') }}</a>
                        <a class="hover:text-secondary-fixed transition-colors" href="{{ route('doctors.index') }}">{{ __('Poli Kandungan (Obgyn)') }}</a>
                        <a class="hover:text-secondary-fixed transition-colors" href="{{ route('doctors.index') }}">{{ __('Poli Jantung & Pembuluh Darah') }}</a>
                        <a class="hover:text-secondary-fixed transition-colors" href="{{ route('doctors.index') }}">{{ __('Laboratorium & Farmasi') }}</a>
                    </div>
                </div>

                <!-- Col 4: HealthQueue Digital -->
                <div class="flex flex-col gap-3">
                    <span class="font-heading text-base text-surface font-semibold">{{ __('HealthQueue Digital') }}</span>
                    <div class="flex flex-col gap-2 text-xs text-surface-container-high">
                        <a class="hover:text-secondary-fixed transition-colors" href="{{ route('queue.index') }}">{{ __('Estimasi Nomor Antrean') }}</a>
                        <a class="hover:text-secondary-fixed transition-colors" href="{{ route('doctors.index') }}">{{ __('Jadwal Praktik Dokter') }}</a>
                        <a class="hover:text-secondary-fixed transition-colors" href="{{ route('checkin.index') }}">{{ __('Panduan Self Check-in') }}</a>
                        <a class="hover:text-secondary-fixed transition-colors" href="{{ route('announcements.index') }}">{{ __('Informasi & Pengumuman') }}</a>
                        <a class="hover:text-secondary-fixed transition-colors" href="{{ route('queue.display') }}" target="_blank">{{ __('Monitor Antrean Publik (TV)') }}</a>
                    </div>
                </div>

                <!-- Col 5: Operational Hours -->
                <div class="flex flex-col gap-3">
                    <span class="font-heading text-base text-surface font-semibold">{{ __('Jam Operasional') }}</span>
                    <div class="flex flex-col gap-2.5 text-xs text-surface-container-high">
                        <div class="bg-surface-container-lowest/10 p-3 rounded-lg flex flex-col gap-1">
                            <span class="font-semibold text-secondary-fixed">{{ __('Instalasi Gawat Darurat (IGD)') }}</span>
                            <span>{{ __('Buka 24 Jam Setiap Hari') }}</span>
                        </div>
                        <div class="bg-surface-container-lowest/10 p-3 rounded-lg flex flex-col gap-1">
                            <span class="font-semibold text-secondary-fixed">{{ __('Poliklinik Rawat Jalan') }}</span>
                            <span>{{ __('Senin - Sabtu: 07.30 - 21.00 WIB') }}</span>
                            <span class="text-surface-variant">{{ __('Minggu & Libur Nasional: Tutup') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-surface-container-high/15 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-surface-container-high/80">
                <p>© {{ date('Y') }} Klinik Ayo Sehat (HealthQueue). {{ __('Seluruh Hak Cipta Dilindungi Undang-Undang.') }}</p>
                <div class="flex items-center gap-6 font-medium">
                    <a class="hover:text-surface transition-colors" href="#">{{ __('Kebijakan Privasi') }}</a>
                    <a class="hover:text-surface transition-colors" href="#">{{ __('Syarat & Ketentuan') }}</a>
                    <a class="hover:text-surface transition-colors" href="#">{{ __('Hak & Kewajiban Pasien') }}</a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
