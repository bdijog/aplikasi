<div>
    <!-- Search & Hero Banner -->
    <section class="relative w-full bg-brand-navy overflow-hidden text-surface">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                <defs>
                    <pattern height="10" id="medical-grid" patternUnits="userSpaceOnUse" width="10">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"></path>
                    </pattern>
                </defs>
                <rect fill="url(#medical-grid)" height="100" width="100"></rect>
            </svg>
        </div>

        <div class="max-w-[75rem] mx-auto px-4 md:px-6 py-12 relative z-10">
            <!-- Breadcrumbs & Live Sync Badge -->
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <nav class="flex items-center gap-1 text-xs font-medium text-surface-container-high">
                    <a class="hover:text-secondary-fixed transition-colors" href="{{ route('home') }}">{{ __('Beranda') }}</a>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <a class="hover:text-secondary-fixed transition-colors" href="{{ route('doctors.index') }}">{{ __('Layanan Pasien') }}</a>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span class="text-secondary-fixed">{{ __('Jadwal Praktik Dokter Terpadu') }}</span>
                </nav>
                <div class="flex items-center gap-2 bg-surface-container-lowest/10 backdrop-blur-md px-3 py-1 rounded-full text-surface-bright text-xs font-medium">
                    <span class="inline-block w-2 h-2 rounded-full bg-status-available animate-pulse"></span>
                    <span>{{ __('Sistem Pembaruan Otomatis: Sinkron Terkoneksi') }}</span>
                </div>
            </div>

            <!-- Headline Block -->
            <div class="max-w-3xl mb-8">
                <h1 class="font-heading text-3xl md:text-4xl font-bold tracking-tight text-surface mb-3">
                    {{ __('Temukan Jadwal Dokter & Reservasi Spesialisasi') }}
                </h1>
                <p class="text-base text-surface-container-high leading-relaxed">
                    {{ __('Akses informasi akurat jadwal poliklinik rawat jalan, ketersediaan kuota waktu nyata, dan booking konsultasi medis langsung tanpa antre di lokasi.') }}
                </p>
            </div>

            <!-- Search & Filters Container -->
            <div class="bg-surface-card rounded-2xl p-6 shadow-xl text-on-surface">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                    <!-- Search Input -->
                    <div class="lg:col-span-4 flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">{{ __('Cari Nama Dokter / Spesialis') }}</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-outline text-[20px]">person_search</span>
                            <input wire:model.live.debounce.300ms="search" class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-surface-container-low text-sm text-on-surface placeholder:text-outline border border-outline-variant/30 focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary transition-all" placeholder="{{ __('Contoh: dr. Sarah, Anak, Jantung...') }}" type="text"/>
                        </div>
                    </div>

                    <!-- Specialty Dropdown -->
                    <div class="lg:col-span-3 flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">{{ __('Pilih Poliklinik Spesialis') }}</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-outline text-[20px]">local_hospital</span>
                            <select wire:model.live="specialty" class="w-full pl-10 pr-8 py-2.5 rounded-xl bg-surface-container-low text-sm text-on-surface appearance-none border border-outline-variant/30 focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary transition-all cursor-pointer">
                                <option value="all">{{ __('Semua Poliklinik') }}</option>
                                <option value="Penyakit Dalam">{{ __('Spesialis Penyakit Dalam') }}</option>
                                <option value="Anak">{{ __('Spesialis Anak (Pediatri)') }}</option>
                                <option value="Obstetri">{{ __('Kandungan (Obgyn)') }}</option>
                                <option value="Jantung">{{ __('Spesialis Jantung & Pembuluh') }}</option>
                                <option value="Mata">{{ __('Spesialis Mata') }}</option>
                                <option value="Umum">{{ __('Dokter Umum') }}</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 pointer-events-none text-outline text-[20px]">expand_more</span>
                        </div>
                    </div>

                    <!-- Date Picker -->
                    <div class="lg:col-span-3 flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">{{ __('Tanggal Kunjungan') }}</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-outline text-[20px]">calendar_today</span>
                            <input wire:model.live="date" class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-surface-container-low text-sm text-on-surface border border-outline-variant/30 focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary transition-all cursor-pointer" type="date"/>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="lg:col-span-2">
                        <button wire:click="resetFilters" type="button" class="w-full py-2.5 px-4 rounded-xl bg-surface-container text-on-surface text-sm font-semibold hover:bg-surface-container-high transition-colors flex items-center justify-center gap-1.5 border border-outline-variant/40">
                            <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                            <span>{{ __('Reset') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Statistics Bar -->
    <div class="w-full bg-surface-container-low border-b border-outline-variant/30 py-4">
        <div class="max-w-[75rem] mx-auto px-4 md:px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 divide-y md:divide-y-0 md:divide-x divide-outline-variant/30">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">medical_services</span>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-on-surface font-heading">{{ $totalDoctors }}</div>
                        <div class="text-xs text-on-surface-variant font-medium">{{ __('Dokter Aktif') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-3 md:pt-0 md:pl-4">
                    <div class="w-10 h-10 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">domain</span>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-on-surface font-heading">{{ $totalSpecialties }}</div>
                        <div class="text-xs text-on-surface-variant font-medium">{{ __('Poliklinik Spesialis') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-3 md:pt-0 md:pl-4">
                    <div class="w-10 h-10 rounded-xl bg-status-available/10 text-status-available flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">event_seat</span>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-status-available font-heading">{{ $totalQuotaToday }}</div>
                        <div class="text-xs text-on-surface-variant font-medium">{{ __('Sisa Kuota Janji Temu') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-3 md:pt-0 md:pl-4">
                    <div class="w-10 h-10 rounded-xl bg-brand-gold/15 text-brand-navy flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">update</span>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-on-surface font-heading">{{ now()->format('H:i') }} WIB</div>
                        <div class="text-xs text-on-surface-variant font-medium">{{ __('Pembaruan Waktu Nyata') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Specialty Quick Select Grid -->
    <section class="w-full max-w-[75rem] mx-auto px-4 md:px-6 py-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-heading font-bold text-lg md:text-xl text-on-surface">{{ __('Pilih Berdasarkan Spesialisasi') }}</h2>
            @if($specialty !== 'all')
                <button wire:click="selectSpecialty('all')" class="text-xs text-primary font-semibold hover:underline">{{ __('Tampilkan Semua') }}</button>
            @endif
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach($specialtiesList as $spec)
                <button wire:click="selectSpecialty('{{ $spec['id'] }}')" type="button" class="group flex flex-col items-center text-center p-4 rounded-xl transition-all border {{ $specialty === $spec['id'] ? 'bg-primary text-white border-primary shadow-md' : 'bg-surface-card border-outline-variant/30 hover:border-primary/50 hover:shadow-sm text-on-surface' }}">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-2 transition-colors {{ $specialty === $spec['id'] ? 'bg-white/20 text-white' : 'bg-surface-container text-primary group-hover:bg-primary/10' }}">
                        <span class="material-symbols-outlined text-[24px]">{{ $spec['icon'] }}</span>
                    </div>
                    <span class="text-xs font-bold leading-tight line-clamp-2">{{ $spec['name'] }}</span>
                    <span class="text-[10px] mt-1 {{ $specialty === $spec['id'] ? 'text-white/80' : 'text-on-surface-variant' }}">{{ $spec['count'] }} {{ __('Dokter') }}</span>
                </button>
            @endforeach
        </div>
    </section>

    <!-- Day Filter Pills -->
    <section class="w-full max-w-[75rem] mx-auto px-4 md:px-6 pb-6">
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <button wire:click="selectDay(null)" type="button" class="px-4 py-2 rounded-xl text-xs font-bold shrink-0 transition-all {{ $day === null ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                {{ __('Semua Hari') }}
            </button>
            @php
                $daysMap = [
                    1 => __('Senin'),
                    2 => __('Selasa'),
                    3 => __('Rabu'),
                    4 => __('Kamis'),
                    5 => __('Jumat'),
                    6 => __('Sabtu'),
                ];
            @endphp
            @foreach($daysMap as $dNum => $dName)
                <button wire:click="selectDay({{ $dNum }})" type="button" class="px-4 py-2 rounded-xl text-xs font-bold shrink-0 transition-all {{ $day === $dNum ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                    {{ $dName }}
                </button>
            @endforeach
        </div>
    </section>

    <!-- Doctor Cards Grid Section -->
    <section class="w-full max-w-[75rem] mx-auto px-4 md:px-6 pb-16">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div class="flex items-baseline gap-2">
                <h3 class="font-heading text-xl font-bold text-on-surface">{{ __('Daftar Dokter Tersedia') }}</h3>
                <span class="text-xs text-on-surface-variant">({{ count($doctors) }} {{ __('Dokter Ditemukan') }})</span>
            </div>
            <!-- Status Legend -->
            <div class="flex items-center gap-4 text-xs font-medium text-on-surface-variant">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-status-available"></span> {{ __('Tersedia (>5)') }}</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-status-limited"></span> {{ __('Terbatas (1-5)') }}</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-status-full"></span> {{ __('Penuh') }}</span>
            </div>
        </div>

        @if(count($doctors) === 0)
            <div class="bg-surface-card rounded-2xl p-12 text-center border border-outline-variant/30">
                <span class="material-symbols-outlined text-outline text-[48px] mb-3">search_off</span>
                <h4 class="font-heading font-bold text-lg text-on-surface mb-1">{{ __('Tidak ada dokter yang cocok dengan kriteria') }}</h4>
                <p class="text-xs text-on-surface-variant mb-4">{{ __('Silakan sesuaikan filter pencarian hari, poliklinik, atau kata kunci Anda.') }}</p>
                <button wire:click="resetFilters" class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold">{{ __('Reset Semua Filter') }}</button>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($doctors as $item)
                    @php
                        $doctor = $item['doctor'];
                        $activeSchedule = $item['activeSchedule'];
                        $quotaRemaining = $item['quotaRemaining'];
                        $maxQuota = $item['maxQuota'];
                        $status = $item['status'];
                    @endphp
                    <article class="bg-surface-card rounded-2xl p-6 shadow-sm hover:shadow-md border border-outline-variant/20 transition-all flex flex-col justify-between">
                        <div>
                            <!-- Header Card: Photo, Room, Name, Specialty -->
                            <div class="flex items-start gap-4 pb-4">
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary/20 to-primary-container/20 text-primary flex items-center justify-center font-heading font-bold text-2xl shrink-0 overflow-hidden shadow-inner border border-primary/20">
                                    @if($doctor->photo)
                                        <img class="w-full h-full object-cover" src="{{ asset('storage/' . $doctor->photo) }}" alt="{{ $doctor->name }}"/>
                                    @else
                                        {{ strtoupper(substr($doctor->name, 0, 2)) }}
                                    @endif
                                </div>
                                <div class="flex flex-col min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2 flex-wrap mb-1">
                                        <span class="px-2 py-0.5 rounded-lg text-xs bg-surface-container-low text-primary font-bold">
                                            {{ __('Poli ') }}{{ $doctor->getTranslation('specialty', app()->getLocale()) }}
                                        </span>
                                        @if($activeSchedule)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-status-available/10 text-status-available">
                                                <span class="w-1.5 h-1.5 rounded-full bg-status-available animate-pulse"></span>
                                                {{ __('Praktik Hari Ini') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container text-on-surface-variant">
                                                {{ __('Tidak Praktik Hari Ini') }}
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="font-heading text-lg font-bold text-on-surface truncate">{{ $doctor->name }}</h4>
                                    <p class="text-xs text-primary font-semibold mt-0.5">{{ $doctor->getTranslation('specialty', app()->getLocale()) }}</p>
                                    <span class="text-[11px] text-on-surface-variant mt-1">STR: {{ $doctor->license_number ?? '31.1.1.100.2.18.098' }}</span>
                                </div>
                            </div>

                            <!-- Schedule & Session Box -->
                            <div class="bg-surface-container-low rounded-xl p-4 mt-2 flex flex-col gap-3">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary text-[18px]">schedule</span>
                                        @if($activeSchedule)
                                            <span class="text-xs font-semibold text-on-surface">
                                                {{ substr($activeSchedule->start_time, 0, 5) }} - {{ substr($activeSchedule->end_time, 0, 5) }} WIB
                                                <span class="text-on-surface-variant font-normal">({{ $activeSchedule->notes ?? 'Poli Reguler' }})</span>
                                            </span>
                                        @else
                                            <span class="text-xs font-medium text-on-surface-variant">{{ __('Lihat jadwal hari lain di profil') }}</span>
                                        @endif
                                    </div>

                                    @if($activeSchedule)
                                        @if($status === 'full')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-status-full/15 text-status-full">
                                                {{ __('Kuota Penuh (0/') }}{{ $maxQuota }})
                                            </span>
                                        @elseif($status === 'limited')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-status-limited/15 text-status-limited">
                                                {{ __('Sisa ') }}{{ $quotaRemaining }}/{{ $maxQuota }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-status-available/15 text-status-available">
                                                {{ __('Sisa Kuota: ') }}{{ $quotaRemaining }}/{{ $maxQuota }}
                                            </span>
                                        @endif
                                    @endif
                                </div>

                                <!-- Mini Weekly Days Strip -->
                                <div class="flex items-center justify-between pt-2 border-t border-outline-variant/20">
                                    <span class="text-[11px] text-on-surface-variant font-medium">{{ __('Jadwal Praktik Mingguan:') }}</span>
                                    <div class="flex gap-1">
                                        @php
                                            $daysAbbr = [1 => 'Sn', 2 => 'Sl', 3 => 'Rb', 4 => 'Km', 5 => 'Jm', 6 => 'Sb'];
                                        @endphp
                                        @foreach($daysAbbr as $dIndex => $dAbbr)
                                            @php
                                                $isScheduled = in_array($dIndex, $item['scheduledDays'], true);
                                            @endphp
                                            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold {{ $isScheduled ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-outline' }}" title="{{ $isScheduled ? __('Praktik') : __('Libur') }}">
                                                {{ $dAbbr }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="flex items-center gap-3 mt-5 pt-2">
                            <a href="{{ route('booking.index', ['doctor_id' => $doctor->id, 'date' => $date]) }}" class="flex-1 bg-primary text-white py-2.5 px-4 rounded-xl text-xs font-bold hover:bg-primary-container transition-all flex items-center justify-center gap-2 shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">event_available</span>
                                <span>{{ __('Reservasi Janji Temu') }}</span>
                            </a>
                            <button wire:click="showDoctorDetail({{ $doctor->id }})" class="px-4 py-2.5 rounded-xl bg-surface-container text-xs font-bold text-primary hover:bg-surface-container-high transition-colors" type="button">
                                <span>{{ __('Profil Lengkap') }}</span>
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <!-- How It Works Section: 4-step Cards -->
    <section class="w-full bg-surface-container-low py-16">
        <div class="max-w-[75rem] mx-auto px-4 md:px-6">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold text-primary uppercase tracking-widest">{{ __('Panduan Pasien') }}</span>
                <h2 class="font-heading text-2xl md:text-3xl font-bold text-on-surface mt-1">{{ __('4 Langkah Mudah Berobat Tanpa Antre') }}</h2>
                <p class="text-sm text-on-surface-variant mt-2">{{ __('Alur pendaftaran digital terintegrasi untuk kenyamanan dan efisiensi waktu Anda dan keluarga.') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="bg-surface-card rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative flex flex-col justify-between border border-outline-variant/30">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-surface-container-low text-primary flex items-center justify-center font-heading text-xl font-bold mb-4">
                            1
                        </div>
                        <h3 class="font-heading text-base font-bold text-on-surface mb-1.5">{{ __('Pilih Dokter & Jadwal') }}</h3>
                        <p class="text-xs text-on-surface-variant leading-relaxed">
                            {{ __('Pilih spesialisasi yang Anda butuhkan, sesuaikan jam praktik dokter dengan ketersediaan kuota waktu nyata.') }}
                        </p>
                    </div>
                    <div class="mt-4 pt-3 text-primary text-xs font-bold flex items-center gap-1 border-t border-outline-variant/20">
                        <span>{{ __('Filter Kebutuhan') }}</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="bg-surface-card rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative flex flex-col justify-between border border-outline-variant/30">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-surface-container-low text-primary flex items-center justify-center font-heading text-xl font-bold mb-4">
                            2
                        </div>
                        <h3 class="font-heading text-base font-bold text-on-surface mb-1.5">{{ __('Isi Data & Buat Janji') }}</h3>
                        <p class="text-xs text-on-surface-variant leading-relaxed">
                            {{ __('Daftarkan pasien baru atau masuk dengan akun pasien, masukkan keluhan, dan langsung dapatkan kode booking QR.') }}
                        </p>
                    </div>
                    <div class="mt-4 pt-3 text-primary text-xs font-bold flex items-center gap-1 border-t border-outline-variant/20">
                        <span>{{ __('Konfirmasi Instan') }}</span>
                        <span class="material-symbols-outlined text-[16px]">qr_code</span>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="bg-surface-card rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative flex flex-col justify-between border border-outline-variant/30">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-surface-container-low text-primary flex items-center justify-center font-heading text-xl font-bold mb-4">
                            3
                        </div>
                        <h3 class="font-heading text-base font-bold text-on-surface mb-1.5">{{ __('Check-in Hari H') }}</h3>
                        <p class="text-xs text-on-surface-variant leading-relaxed">
                            {{ __('Lakukan self check-in via gawai ponsel saat tiba di klinik, atau masukkan kode booking pada kios anjungan mandiri.') }}
                        </p>
                    </div>
                    <div class="mt-4 pt-3 text-primary text-xs font-bold flex items-center gap-1 border-t border-outline-variant/20">
                        <span>{{ __('30 Menit Sebelum Sesi') }}</span>
                        <span class="material-symbols-outlined text-[16px]">touch_app</span>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="bg-surface-card rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative flex flex-col justify-between border border-outline-variant/30">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-primary text-white flex items-center justify-center font-heading text-xl font-bold mb-4 shadow-sm">
                            4
                        </div>
                        <h3 class="font-heading text-base font-bold text-on-surface mb-1.5">{{ __('Pantau Antrean Live') }}</h3>
                        <p class="text-xs text-on-surface-variant leading-relaxed">
                            {{ __('Pantau pergerakan nomor panggilan ruang poliklinik secara live dari ruang tunggu dengan audio pengumuman.') }}
                        </p>
                    </div>
                    <div class="mt-4 pt-3 text-secondary text-xs font-bold flex items-center gap-1 border-t border-outline-variant/20">
                        <span>{{ __('Notifikasi Suara Audio') }}</span>
                        <span class="material-symbols-outlined text-[16px]">volume_up</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reassurance & WhatsApp Admission Banner -->
    <section class="w-full bg-surface-container-low pb-16">
        <div class="max-w-[75rem] mx-auto px-4 md:px-6">
            <div class="bg-gradient-to-r from-primary to-brand-navy rounded-3xl p-8 text-surface flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-surface-container-lowest/15 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[36px] text-brand-gold">support_agent</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-xl font-bold">{{ __('Butuh Bantuan Pendaftaran Jadwal Dokter?') }}</h3>
                        <p class="text-xs md:text-sm text-surface-container-high mt-1 max-w-xl">
                            {{ __('Petugas admisi kami siap membantu pertanyaan terkait rujukan BPJS, asuransi swasta, atau janji temu dokter spesialis melalui WhatsApp.') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="https://wa.me/6281234567890" target="_blank" class="bg-brand-gold text-brand-navy px-6 py-3 rounded-xl text-xs font-bold hover:bg-yellow-400 transition-colors shadow-md flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">chat</span>
                        <span>{{ __('WhatsApp Admisi: 0812-3456-7890') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Doctor Detail Modal -->
    @if($selectedDoctor)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:click.self="closeDoctorDetail">
            <div class="bg-surface-card rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-outline-variant/30 p-6 md:p-8 relative animate-in fade-in zoom-in duration-200">
                <button wire:click="closeDoctorDetail" class="absolute top-6 right-6 p-2 rounded-full hover:bg-surface-container text-on-surface-variant transition-colors" aria-label="Tutup Modal">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>

                <div class="flex items-start gap-5 mb-6">
                    <div class="w-20 h-20 rounded-2xl bg-primary/15 text-primary flex items-center justify-center font-heading font-bold text-2xl shrink-0 overflow-hidden border border-primary/20">
                        @if($selectedDoctor->photo)
                            <img class="w-full h-full object-cover" src="{{ asset('storage/' . $selectedDoctor->photo) }}" alt="{{ $selectedDoctor->name }}"/>
                        @else
                            {{ strtoupper(substr($selectedDoctor->name, 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs bg-primary/10 text-primary font-bold">
                            {{ $selectedDoctor->getTranslation('specialty', app()->getLocale()) }}
                        </span>
                        <h3 class="font-heading text-xl font-bold text-on-surface mt-1.5">{{ $selectedDoctor->name }}</h3>
                        <p class="text-xs text-on-surface-variant mt-0.5">STR: {{ $selectedDoctor->license_number ?? '-' }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <h4 class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">{{ __('Profil & Keahlian Klinis') }}</h4>
                        <p class="text-xs text-on-surface leading-relaxed">
                            {{ $selectedDoctor->getTranslation('bio', app()->getLocale()) ?: __('Dokter spesialis berpengalaman di Klinik Ayo Sehat yang berdedikasi memberikan pelayanan medis komprehensif, tepat, dan ramah keluarga.') }}
                        </p>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3">{{ __('Jadwal Praktik Rutin') }}</h4>
                        <div class="space-y-2">
                            @forelse($selectedDoctor->schedules as $sched)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low border border-outline-variant/20 text-xs">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-primary text-[20px]">calendar_month</span>
                                        <div>
                                            <div class="font-bold text-on-surface">
                                                {{ match($sched->day_of_week) {
                                                    1 => __('Senin'),
                                                    2 => __('Selasa'),
                                                    3 => __('Rabu'),
                                                    4 => __('Kamis'),
                                                    5 => __('Jumat'),
                                                    6 => __('Sabtu'),
                                                    7 => __('Minggu'),
                                                    default => '-'
                                                } }}
                                            </div>
                                            <div class="text-on-surface-variant">{{ $sched->notes ?? __('Poli Reguler') }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-primary">{{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }} WIB</div>
                                        <div class="text-[10px] text-on-surface-variant">{{ __('Maks: ') }}{{ $sched->max_patients }} {{ __('pasien') }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-xs text-on-surface-variant p-3 bg-surface-container-low rounded-xl text-center">
                                    {{ __('Belum ada jadwal praktik aktif.') }}
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <a href="{{ route('booking.index', ['doctor_id' => $selectedDoctor->id]) }}" class="flex-1 bg-primary text-white py-3 px-4 rounded-xl text-xs font-bold hover:bg-primary-container transition-all flex items-center justify-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">event_available</span>
                            <span>{{ __('Buat Janji Temu dengan Dokter Ini') }}</span>
                        </a>
                        <button wire:click="closeDoctorDetail" class="px-5 py-3 rounded-xl bg-surface-container text-xs font-bold text-on-surface hover:bg-surface-container-high transition-colors">
                            {{ __('Tutup') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
