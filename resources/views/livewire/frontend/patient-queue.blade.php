<div class="max-w-[75rem] mx-auto px-4 md:px-6 py-8" wire:poll.8s>
    <!-- Page Header & Quick Search Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant font-medium mb-2">
                <a class="hover:text-primary transition-colors" href="{{ route('home') }}">{{ __('Beranda') }}</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-bold">{{ __('Status Antrean Pasien') }}</span>
            </nav>
            <h1 class="font-heading text-2xl md:text-3xl font-bold text-on-surface">
                {{ __('Pantau Antrean Pasien Waktu Nyata') }}
            </h1>
            <p class="text-xs text-on-surface-variant mt-1">
                {{ __('Pergerakan antrean live terkoneksi langsung dengan sistem display poliklinik.') }}
            </p>
        </div>

        <!-- Ticket Search Box & TV Monitor Button -->
        <div class="flex items-center gap-3">
            <a href="{{ route('queue.display') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-brand-navy text-white text-xs font-bold hover:bg-brand-navy/90 flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">tv</span>
                <span>{{ __('Buka Layar TV') }}</span>
            </a>

            <form wire:submit="searchTicket" class="flex items-center gap-2">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                    <input wire:model="ticketCode" type="text" placeholder="{{ __('No. Tiket / Booking / NIK') }}" class="w-48 sm:w-56 pl-9 pr-3 py-2 rounded-xl bg-surface-container-low text-xs border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"/>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container shadow-sm">
                    {{ __('Cari') }}
                </button>
            </form>
        </div>
    </div>

    @if($errors->has('ticketCode'))
        <div class="mb-6 p-4 rounded-xl bg-error/10 border border-error/30 text-error text-xs flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <span>{{ $errors->first('ticketCode') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Main Patient Ticket Column (Span 8) -->
        <div class="lg:col-span-8 flex flex-col gap-6">

            @if($activeTicket)
                <!-- Primary Ticket Card -->
                <div class="bg-surface-card rounded-3xl p-6 md:p-8 shadow-sm border border-outline-variant/30">
                    <!-- Ticket Header -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-6 border-b border-outline-variant/20">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-[28px]">confirmation_number</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-primary uppercase tracking-wider">{{ __('Tiket Antrean Poliklinik') }}</span>
                                <h2 class="font-heading text-lg font-bold text-on-surface">{{ $activeTicket->appointment?->patient?->name ?? __('Pasien Terdaftar') }}</h2>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-xs font-medium text-on-surface-variant">
                            <span>{{ __('Kode Booking: ') }}<strong class="text-on-surface">{{ $activeTicket->appointment?->booking_code ?? '-' }}</strong></span>
                            <span>•</span>
                            <span>{{ __('Waktu: ') }}<strong class="text-on-surface">{{ $activeTicket->created_at ? $activeTicket->created_at->format('H:i') : now()->format('H:i') }} WIB</strong></span>
                        </div>
                    </div>

                    <!-- Queue Number & Current Serving Matrix -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center bg-surface-container-low/70 p-6 rounded-2xl my-6 border border-outline-variant/20">
                        <!-- Left: Patient's Queue Number -->
                        <div class="md:col-span-5 flex flex-col">
                            <span class="text-[11px] font-bold text-on-surface-variant uppercase tracking-wider">{{ __('Nomor Antrean Anda') }}</span>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="font-heading text-4xl md:text-5xl text-primary font-black tracking-tight">{{ $activeTicket->display_number }}</span>
                                <span class="text-xs text-on-surface-variant font-medium">/ 25 {{ __('Kuota') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 mt-2 text-status-available text-xs font-semibold">
                                <span class="material-symbols-outlined text-[16px]">hourglass_bottom</span>
                                <span>{{ __('Estimasi Tunggu: ± ') }}{{ max(5, $remainingBefore * 12) }} {{ __('Menit lagi') }}</span>
                            </div>
                        </div>

                        <!-- Center Divider Arrow -->
                        <div class="hidden md:block md:col-span-2 text-center">
                            <div class="w-10 h-10 mx-auto rounded-full bg-surface-container-high flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                            </div>
                        </div>

                        <!-- Right: Currently Called Live -->
                        <div class="md:col-span-5 flex flex-col bg-surface-card p-4 rounded-xl shadow-xs border border-outline-variant/20">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">{{ __('Sedang Dipanggil') }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-error/15 text-error text-[10px] font-bold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-error animate-ping"></span>
                                    <span>{{ __('Live Konsultasi') }}</span>
                                </span>
                            </div>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="font-heading text-3xl font-bold text-brand-navy">
                                    {{ $currentlyServingTicket?->display_number ?? 'A-008' }}
                                </span>
                                <span class="text-xs text-on-surface-variant font-medium">
                                    ({{ $activeTicket->counter ?? 'Ruang Poli 204' }})
                                </span>
                            </div>
                            <p class="text-[11px] text-on-surface-variant mt-1">
                                @if($remainingBefore > 0)
                                    {{ __('Sisa ') }}<strong>{{ $remainingBefore }} {{ __('pasien') }}</strong>{{ __(' sebelum giliran Anda') }}
                                @else
                                    <span class="text-status-available font-bold">{{ __('Giliran Anda berikutnya!') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Doctor & Room Details -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 bg-surface-container-low rounded-2xl border border-outline-variant/20 mb-6">
                        <div class="w-16 h-16 rounded-xl bg-primary/20 text-primary flex items-center justify-center font-heading font-bold text-xl shrink-0 overflow-hidden">
                            @if($activeTicket->doctor?->photo)
                                <img class="w-full h-full object-cover" src="{{ asset('storage/' . $activeTicket->doctor->photo) }}" alt="{{ $activeTicket->doctor->name }}"/>
                            @else
                                {{ strtoupper(substr($activeTicket->doctor?->name ?? 'Dokter', 0, 2)) }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-heading text-base font-bold text-on-surface">{{ $activeTicket->doctor?->name ?? 'dr. Spesialis' }}</h3>
                                <span class="px-2 py-0.5 bg-secondary-fixed text-on-secondary-fixed text-[10px] rounded font-bold">{{ __('Dokter Penanggung Jawab') }}</span>
                            </div>
                            <p class="text-xs text-primary font-semibold mt-0.5">
                                {{ __('Poli ') }}{{ $activeTicket->doctor?->getTranslation('specialty', app()->getLocale()) ?? 'Spesialis' }}
                            </p>
                            <div class="flex flex-wrap items-center gap-4 mt-1.5 text-xs text-on-surface-variant">
                                <span class="flex items-center gap-1 font-semibold text-primary">
                                    <span class="material-symbols-outlined text-[16px]">meeting_room</span>
                                    <span>{{ $activeTicket->counter ?? 'Ruang 204 Lt. 2' }}</span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                                    <span>{{ __('Sesi: 08:00 - 12:00 WIB') }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 5-Step Visual Progress Tracker -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xs font-bold text-brand-navy uppercase tracking-wider">{{ __('Alur Pelayanan Antrean') }}</span>
                            <span class="text-[11px] text-on-surface-variant">{{ __('Rata-rata 10-15 menit / pasien') }}</span>
                        </div>

                        @php
                            $ticketStatus = $activeTicket->status->value;
                            $stepIndex = match($ticketStatus) {
                                'waiting' => 2,
                                'serving' => 4,
                                'completed' => 5,
                                'cancelled' => 0,
                                default => 2
                            };
                        @endphp

                        <div class="grid grid-cols-5 gap-2 pt-2">
                            <!-- 1. Checked-in -->
                            <div class="flex flex-col items-center text-center">
                                <div class="w-8 h-8 rounded-full bg-status-available text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                    <span class="material-symbols-outlined text-[18px]">check</span>
                                </div>
                                <span class="text-[11px] font-bold text-on-surface mt-1.5">{{ __('Checked-in') }}</span>
                                <span class="text-[10px] text-on-surface-variant">{{ __('Selesai') }}</span>
                            </div>

                            <!-- 2. Menunggu -->
                            <div class="flex flex-col items-center text-center {{ $stepIndex >= 2 ? '' : 'opacity-40' }}">
                                <div class="w-8 h-8 rounded-full {{ $stepIndex === 2 ? 'bg-status-limited text-white ring-4 ring-status-limited/20' : ($stepIndex > 2 ? 'bg-status-available text-white' : 'bg-surface-container text-outline') }} flex items-center justify-center font-bold text-xs shadow-xs">
                                    @if($stepIndex > 2)
                                        <span class="material-symbols-outlined text-[18px]">check</span>
                                    @else
                                        <span class="material-symbols-outlined text-[18px]">hourglass_top</span>
                                    @endif
                                </div>
                                <span class="text-[11px] font-bold {{ $stepIndex === 2 ? 'text-status-limited' : 'text-on-surface' }} mt-1.5">{{ __('Menunggu') }}</span>
                                <span class="text-[10px] text-on-surface-variant">{{ $stepIndex === 2 ? __('Ruang Tunggu') : '-' }}</span>
                            </div>

                            <!-- 3. Dipanggil -->
                            <div class="flex flex-col items-center text-center {{ $stepIndex >= 3 ? '' : 'opacity-40' }}">
                                <div class="w-8 h-8 rounded-full {{ $stepIndex === 3 ? 'bg-primary text-white ring-4 ring-primary/20' : ($stepIndex > 3 ? 'bg-status-available text-white' : 'bg-surface-container text-outline') }} flex items-center justify-center font-bold text-xs">
                                    @if($stepIndex > 3)
                                        <span class="material-symbols-outlined text-[18px]">check</span>
                                    @else
                                        3
                                    @endif
                                </div>
                                <span class="text-[11px] font-bold text-on-surface mt-1.5">{{ __('Dipanggil') }}</span>
                                <span class="text-[10px] text-on-surface-variant">{{ __('Layar/Audio') }}</span>
                            </div>

                            <!-- 4. Konsultasi -->
                            <div class="flex flex-col items-center text-center {{ $stepIndex >= 4 ? '' : 'opacity-40' }}">
                                <div class="w-8 h-8 rounded-full {{ $stepIndex === 4 ? 'bg-primary text-white ring-4 ring-primary/20' : ($stepIndex > 4 ? 'bg-status-available text-white' : 'bg-surface-container text-outline') }} flex items-center justify-center font-bold text-xs">
                                    @if($stepIndex > 4)
                                        <span class="material-symbols-outlined text-[18px]">check</span>
                                    @else
                                        4
                                    @endif
                                </div>
                                <span class="text-[11px] font-bold text-on-surface mt-1.5">{{ __('Konsultasi') }}</span>
                                <span class="text-[10px] text-on-surface-variant">{{ __('Di Ruang Poli') }}</span>
                            </div>

                            <!-- 5. Selesai -->
                            <div class="flex flex-col items-center text-center {{ $stepIndex >= 5 ? '' : 'opacity-40' }}">
                                <div class="w-8 h-8 rounded-full {{ $stepIndex === 5 ? 'bg-status-available text-white' : 'bg-surface-container text-outline' }} flex items-center justify-center font-bold text-xs">
                                    5
                                </div>
                                <span class="text-[11px] font-bold text-on-surface mt-1.5">{{ __('Selesai') }}</span>
                                <span class="text-[10px] text-on-surface-variant">{{ __('Farmasi/Kasir') }}</span>
                            </div>
                        </div>

                        <!-- Continuous Progress Line -->
                        <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden mt-4">
                            <div class="bg-primary h-full rounded-full transition-all duration-500" style="width: {{ match($stepIndex) { 1 => '20%', 2 => '40%', 3 => '60%', 4 => '80%', 5 => '100%', default => '40%' } }}"></div>
                        </div>
                    </div>

                    <!-- Audio Notification Call & Actions -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button onclick="playVoiceCall('{{ $activeTicket->display_number }}', '{{ $activeTicket->counter ?? 'Ruang Poli 204' }}')" type="button" class="flex-1 min-w-[200px] py-3 px-4 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container shadow-sm flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-[20px]">volume_up</span>
                            <span>{{ __('Dengarkan Panggilan Suara') }}</span>
                        </button>

                        <button onclick="alert('Petunjuk Arah: Naik tangga / lift ke Lantai 2, belok kanan menyusuri koridor Poli Spesialis. Ruang 204 berada di sebelah kanan.')" type="button" class="flex-1 min-w-[180px] py-3 px-4 rounded-xl bg-surface-container text-primary text-xs font-bold hover:bg-surface-container-high flex items-center justify-center gap-2 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">directions</span>
                            <span>{{ __('Panduan Ruang') }}</span>
                        </button>

                        <button wire:click="cancelQueue({{ $activeTicket->id }})" wire:confirm="{{ __('Apakah Anda yakin ingin membatalkan nomor antrean ini?') }}" type="button" class="py-3 px-4 rounded-xl text-error hover:bg-error/10 text-xs font-bold transition-colors">
                            {{ __('Batalkan Antrean') }}
                        </button>
                    </div>
                </div>
            @else
                <!-- No Active Ticket Card -->
                <div class="bg-surface-card rounded-3xl p-12 text-center border border-outline-variant/30">
                    <div class="w-16 h-16 rounded-full bg-surface-container text-outline flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-[36px]">confirmation_number</span>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-on-surface mb-2">{{ __('Belum Ada Antrean yang Dipilih') }}</h3>
                    <p class="text-xs text-on-surface-variant max-w-md mx-auto mb-6">
                        {{ __('Silakan masukkan nomor antrean, kode booking, atau NIK Anda pada kolom pencarian di atas, atau buat reservasi janji temu dokter terlebih dahulu.') }}
                    </p>
                    <a href="{{ route('booking.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        <span>{{ __('Daftar & Ambil Antrean') }}</span>
                    </a>
                </div>
            @endif

            <!-- Waiting Room Analytics Card -->
            <div class="bg-surface-card rounded-2xl p-6 shadow-sm border border-outline-variant/30">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[22px]">analytics</span>
                        <h3 class="font-heading text-sm font-bold text-on-surface">{{ __('Statistik Ruang Tunggu Poliklinik') }}</h3>
                    </div>
                    <span class="text-[11px] text-on-surface-variant">{{ __('Berdasarkan data hari ini') }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/20">
                        <span class="text-[11px] text-on-surface-variant block mb-1">{{ __('Rata-rata Konsultasi') }}</span>
                        <span class="font-heading text-xl font-bold text-primary">12 {{ __('Menit') }}</span>
                    </div>
                    <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/20">
                        <span class="text-[11px] text-on-surface-variant block mb-1">{{ __('Ketepatan Jadwal Dokter') }}</span>
                        <span class="font-heading text-xl font-bold text-status-available">98%</span>
                    </div>
                    <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/20">
                        <span class="text-[11px] text-on-surface-variant block mb-1">{{ __('Pasien Selesai Hari Ini') }}</span>
                        <span class="font-heading text-xl font-bold text-on-surface">42 {{ __('Pasien') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Other Clinics Live Status & Support (Span 4) -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <!-- All Clinics Overview -->
            <div class="bg-surface-card rounded-2xl p-6 shadow-sm border border-outline-variant/30">
                <div class="flex items-center justify-between pb-3 border-b border-outline-variant/20 mb-4">
                    <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-primary">{{ __('Antrean Poliklinik Lainnya') }}</h3>
                    <span class="w-2 h-2 rounded-full bg-status-available animate-pulse"></span>
                </div>

                <div class="space-y-3">
                    @foreach($clinicsSummary as $clinic)
                        <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/20 flex items-center justify-between">
                            <div>
                                <h4 class="font-heading text-xs font-bold text-on-surface">{{ $clinic['name'] }}</h4>
                                <span class="text-[11px] text-on-surface-variant">{{ $clinic['room'] }}</span>
                            </div>
                            <div class="text-right">
                                <div class="font-heading text-base font-black text-primary">{{ $clinic['current'] }}</div>
                                <span class="text-[10px] text-status-limited font-semibold">{{ $clinic['waiting'] }} {{ __('menunggu') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-3 border-t border-outline-variant/20">
                    <a href="{{ route('queue.display') }}" target="_blank" class="w-full py-2.5 rounded-xl bg-surface-container text-xs font-bold text-primary hover:bg-surface-container-high transition-colors flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">fullscreen</span>
                        <span>{{ __('Lihat Tampilan Penuh Monitor TV') }}</span>
                    </a>
                </div>
            </div>

            <!-- Emergency & Admission Help -->
            <div class="bg-brand-navy rounded-2xl p-6 text-white shadow-md">
                <div class="flex items-center gap-3 mb-3">
                    <span class="material-symbols-outlined text-brand-gold text-[28px]">notifications_active</span>
                    <h4 class="font-heading text-sm font-bold">{{ __('Panggilan Terlewat?') }}</h4>
                </div>
                <p class="text-xs text-surface-container-high leading-relaxed mb-4">
                    {{ __('Jika nomor antrean Anda terlewat lebih dari 3 panggilan, harap segera melapor ke perawat di meja informasi ruang tunggu agar dipanggil kembali.') }}
                </p>
                <div class="p-3 rounded-xl bg-white/10 text-xs flex items-center justify-between">
                    <span>{{ __('Meja Perawat Lobi 2:') }}</span>
                    <strong class="text-brand-gold">{{ __('Ext. 201') }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function playVoiceCall(ticketNumber, counterName) {
        // First play chime tone
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const now = ctx.currentTime;
            
            // Note 1 (D5 - 587Hz)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.frequency.value = 587.33;
            gain1.gain.setValueAtTime(0.3, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.5);

            // Note 2 (A5 - 880Hz)
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.frequency.value = 880.00;
            gain2.gain.setValueAtTime(0.3, now + 0.25);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.25);
            osc2.stop(now + 0.8);
        } catch (e) {
            console.log('AudioContext not supported, falling back');
        }

        // Web Speech API synthesis
        setTimeout(() => {
            if ('speechSynthesis' in window) {
                const isEn = '{{ app()->getLocale() }}' === 'en';
                const formattedNum = ticketNumber.replace('-', ' ');
                const text = isEn 
                    ? `Queue number ${formattedNum}, please proceed to ${counterName}`
                    : `Nomor antrean ${formattedNum}, silakan menuju ke ${counterName}`;
                
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = isEn ? 'en-US' : 'id-ID';
                utterance.rate = 0.9;
                window.speechSynthesis.speak(utterance);
            }
        }, 800);
    }
</script>
@endpush
