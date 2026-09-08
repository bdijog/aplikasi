<div class="max-w-[75rem] mx-auto px-4 md:px-6 py-8">
    <!-- Breadcrumb & Header -->
    <div class="mb-8">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant font-medium mb-3">
            <a class="hover:text-primary transition-colors" href="{{ route('home') }}">{{ __('Beranda') }}</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">{{ __('Self Check-in Mandiri') }}</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl md:text-3xl font-bold text-on-surface">
                    {{ __('Anjungan Self Check-in Mandiri') }}
                </h1>
                <p class="text-xs text-on-surface-variant mt-1">
                    {{ __('Konfirmasi kehadiran kedatangan Anda untuk menerbitkan nomor antrean fisik poliklinik secara instan.') }}
                </p>
            </div>
            
            <div class="flex items-center gap-2 bg-surface-container-low px-3 py-1.5 rounded-full border border-outline-variant/30 text-xs font-semibold text-primary">
                <span class="material-symbols-outlined text-[18px]">verified</span>
                <span>{{ __('Kiosk Lobi Utama & Online Mobile') }}</span>
            </div>
        </div>
    </div>

    <!-- Main Grid: 7 Cols Intake Form / 5 Cols Resulting Ticket -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Intake Column (Span 7) -->
        <div class="lg:col-span-7 flex flex-col gap-6">

            <!-- Check-in Method Tab Selector -->
            <div class="bg-surface-card rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30">
                <div class="flex items-center p-1 bg-surface-container rounded-xl mb-6">
                    <button wire:click="$set('activeTab', 'manual')" type="button" class="flex-1 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $activeTab === 'manual' ? 'bg-surface-card text-primary shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[18px]">keyboard</span>
                        <span>{{ __('Ketik Manual (Booking / NIK)') }}</span>
                    </button>
                    <button wire:click="$set('activeTab', 'scan')" type="button" class="flex-1 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $activeTab === 'scan' ? 'bg-surface-card text-primary shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[18px]">qr_code_scanner</span>
                        <span>{{ __('Pindai Kamera QR Barcode') }}</span>
                    </button>
                </div>

                @if($activeTab === 'manual')
                    <!-- Manual Input Form -->
                    <form wire:submit="verifyCheckIn" class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                {{ __('Kode Booking, NIK Pasien, atau Nomor Rekam Medis (RM) *') }}
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-4 text-primary text-[26px]">search_check_2</span>
                                <input wire:model="bookingCode" type="text" placeholder="{{ __('BK-YYYYMMDD-XXXX atau 16 digit NIK...') }}" class="w-full pl-13 pr-10 py-3.5 rounded-xl bg-surface-container-low font-heading text-base md:text-lg text-on-surface font-semibold tracking-wider placeholder:text-outline border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"/>
                                @if(!empty($bookingCode))
                                    <button wire:click="$set('bookingCode', '')" type="button" class="absolute right-3 text-outline hover:text-on-surface p-1">
                                        <span class="material-symbols-outlined text-[20px]">cancel</span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Shortcut Simulator Helpers -->
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="text-on-surface-variant font-medium">{{ __('Uji simulasi cepat:') }}</span>
                            @php
                                $sampleAppt = \App\Models\Appointment::latest()->first();
                            @endphp
                            @if($sampleAppt)
                                <button wire:click="fillSample('{{ $sampleAppt->booking_code }}')" type="button" class="px-2.5 py-1 rounded-lg bg-surface-container text-primary font-bold hover:bg-surface-container-high transition-colors">
                                    {{ $sampleAppt->booking_code }} ({{ $sampleAppt->doctor?->name ?? 'Dokter' }})
                                </button>
                                @if($sampleAppt->patient?->national_id)
                                    <button wire:click="fillSample('{{ $sampleAppt->patient->national_id }}')" type="button" class="px-2.5 py-1 rounded-lg bg-surface-container text-on-surface-variant hover:text-primary transition-colors">
                                        NIK: {{ substr($sampleAppt->patient->national_id, 0, 8) }}****
                                    </button>
                                @endif
                            @endif
                        </div>

                        @if($errorMessage)
                            <div class="p-4 rounded-xl bg-error/10 border border-error/30 text-error text-xs flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px]">error</span>
                                <span>{{ $errorMessage }}</span>
                            </div>
                        @endif

                        <!-- Privacy Security Stamp -->
                        <div class="p-3.5 rounded-xl bg-surface-container-low flex items-start gap-2.5 border border-outline-variant/20">
                            <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">verified_user</span>
                            <p class="text-[11px] text-on-surface-variant leading-relaxed">
                                {{ __('Sistem terhubung langsung dengan rekam medis elektronik Klinik Ayo Sehat. Pastikan Anda telah berada di lokasi klinik.') }}
                            </p>
                        </div>

                        <!-- Action Submit Button -->
                        <div class="pt-2">
                            <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-primary text-white font-heading text-sm font-bold hover:bg-primary-container shadow-md flex items-center justify-center gap-2 transition-all">
                                <span class="material-symbols-outlined text-[22px]">how_to_reg</span>
                                <span>{{ __('Verifikasi & Terbitkan Tiket Antrean') }}</span>
                            </button>
                        </div>
                    </form>
                @else
                    <!-- QR Scanner Simulated Area -->
                    <div class="flex flex-col items-center justify-center text-center gap-4 py-4">
                        <div>
                            <h3 class="font-heading text-base font-bold text-on-surface">{{ __('Arahkan QR Code Booking Anda') }}</h3>
                            <p class="text-xs text-on-surface-variant mt-0.5">{{ __('Dekatkan kode QR pada bukti PDF atau WhatsApp ke lensa sensor kios.') }}</p>
                        </div>

                        <!-- Viewfinder Graphic Frame -->
                        <div class="relative w-64 h-64 rounded-2xl bg-brand-navy flex items-center justify-center overflow-hidden shadow-inner border border-white/20">
                            <div class="absolute inset-0 bg-gradient-to-b from-primary/20 via-transparent to-primary/20 pointer-events-none"></div>
                            <!-- Laser Line -->
                            <div class="w-full h-1 bg-secondary-fixed shadow-[0_0_12px_#89f5e7] absolute top-12 animate-pulse"></div>
                            <!-- Reticles -->
                            <div class="absolute top-4 left-4 w-6 h-6 rounded-tl-lg border-t-4 border-l-4 border-secondary-fixed"></div>
                            <div class="absolute top-4 right-4 w-6 h-6 rounded-tr-lg border-t-4 border-r-4 border-secondary-fixed"></div>
                            <div class="absolute bottom-4 left-4 w-6 h-6 rounded-bl-lg border-b-4 border-l-4 border-secondary-fixed"></div>
                            <div class="absolute bottom-4 right-4 w-6 h-6 rounded-br-lg border-b-4 border-r-4 border-secondary-fixed"></div>

                            <div class="flex flex-col items-center gap-1 text-white z-10">
                                <span class="material-symbols-outlined text-[44px] text-secondary-fixed">qr_code_2</span>
                                <span class="text-[11px] text-surface-container">{{ __('Jarak 15 - 25 cm') }}</span>
                            </div>
                        </div>

                        <button wire:click="simulateScanSuccess" type="button" class="px-6 py-2.5 rounded-xl bg-surface-container text-xs font-bold text-primary hover:bg-surface-container-high transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">bolt</span>
                            <span>{{ __('Simulasikan Pemindaian Berhasil') }}</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Business Rules Checklist -->
            <div class="p-6 rounded-2xl bg-surface-container-low border border-outline-variant/30 flex flex-col gap-3">
                <span class="text-xs font-bold text-on-surface flex items-center gap-1.5 uppercase tracking-wider">
                    <span class="material-symbols-outlined text-primary text-[18px]">rule</span>
                    <span>{{ __('Ketentuan & Prosedur Check-in Mandiri') }}</span>
                </span>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="p-3 rounded-xl bg-surface-card flex items-start gap-2 shadow-xs border border-outline-variant/20">
                        <span class="material-symbols-outlined text-status-available text-[18px] shrink-0">check_circle</span>
                        <span class="text-xs text-on-surface-variant">{{ __('Hanya berlaku pada hari H tanggal praktik dokter.') }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-card flex items-start gap-2 shadow-xs border border-outline-variant/20">
                        <span class="material-symbols-outlined text-status-available text-[18px] shrink-0">check_circle</span>
                        <span class="text-xs text-on-surface-variant">{{ __('Maksimal waktu check-in 15 menit sebelum jam sesi dimulai.') }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-card flex items-start gap-2 shadow-xs border border-outline-variant/20">
                        <span class="material-symbols-outlined text-status-available text-[18px] shrink-0">check_circle</span>
                        <span class="text-xs text-on-surface-variant">{{ __('Pasien BPJS siapkan surat rujukan untuk verifikasi loket.') }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Side: Real-Time Issued Ticket Result Card (Span 5) -->
        <div class="lg:col-span-5 flex flex-col gap-6">

            @if($issuedTicket && $verifiedAppointment)
                <!-- Real-time Issued Ticket Card -->
                <div class="rounded-3xl bg-surface-card shadow-xl overflow-hidden border border-outline-variant/30 animate-in fade-in zoom-in duration-200">
                    <!-- Ticket Top Header -->
                    <div class="p-6 bg-brand-navy text-surface relative">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-brand-gold text-[22px]">receipt_long</span>
                                <span class="text-xs font-bold text-brand-gold uppercase tracking-wider">{{ __('Tiket Antrean Poliklinik') }}</span>
                            </div>
                            <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-status-available/20 text-status-available text-xs font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-status-available"></span>
                                <span>{{ __('Valid Terverifikasi') }}</span>
                            </div>
                        </div>

                        <div class="flex items-baseline justify-between pt-2">
                            <div>
                                <span class="text-xs text-surface-container-high block">{{ __('Nomor Antrean Terbit') }}</span>
                                <div class="font-heading text-5xl font-black text-white tracking-tight leading-none mt-1">
                                    {{ $issuedTicket->display_number }}
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-surface-container-high block">{{ __('Estimasi Dipanggil') }}</span>
                                <div class="font-heading text-xl font-bold text-brand-gold mt-1">
                                    ~ {{ substr($verifiedAppointment->estimated_time, 0, 5) }} WIB
                                </div>
                                <span class="text-[10px] text-surface-container-high">{{ __('(Sesuai Sesi Praktik)') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Perforation Cut Visual Divider -->
                    <div class="relative w-full h-4 bg-surface-card flex items-center justify-between px-2 overflow-hidden">
                        <div class="w-4 h-4 rounded-full bg-surface -ml-4"></div>
                        <div class="flex-1 border-t-2 border-dashed border-outline-variant/40 mx-2"></div>
                        <div class="w-4 h-4 rounded-full bg-surface -mr-4"></div>
                    </div>

                    <!-- Ticket Details Body -->
                    <div class="p-6 space-y-4 text-xs">
                        <div class="grid grid-cols-2 gap-3 pb-3 border-b border-outline-variant/15">
                            <div>
                                <span class="text-on-surface-variant block">{{ __('Nama Pasien:') }}</span>
                                <p class="font-bold text-on-surface text-sm mt-0.5">{{ $verifiedAppointment->patient->name }}</p>
                                <p class="text-[10px] text-outline mt-0.5">NIK: {{ substr($verifiedAppointment->patient->national_id ?? '3201xxxxxxxxxxxx', 0, 6) }}******</p>
                            </div>
                            <div>
                                <span class="text-on-surface-variant block">{{ __('No. Rekam Medis:') }}</span>
                                <p class="font-bold text-primary text-sm mt-0.5">{{ $verifiedAppointment->patient->medical_record_number }}</p>
                                <span class="text-[10px] text-status-available font-semibold">{{ __('BPJS Aktif / Terdaftar') }}</span>
                            </div>
                        </div>

                        <div class="pb-3 border-b border-outline-variant/15">
                            <span class="text-on-surface-variant block">{{ __('Dokter Spesialis:') }}</span>
                            <p class="font-bold text-on-surface text-sm mt-0.5">{{ $verifiedAppointment->doctor->name }}</p>
                            <p class="text-xs text-primary font-semibold">{{ $verifiedAppointment->doctor->getTranslation('specialty', app()->getLocale()) }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pb-3 border-b border-outline-variant/15">
                            <div>
                                <span class="text-on-surface-variant block">{{ __('Lokasi Pemeriksaan:') }}</span>
                                <p class="font-bold text-primary mt-0.5">{{ $issuedTicket->counter ?? 'Ruang 204 Lt. 2' }}</p>
                            </div>
                            <div>
                                <span class="text-on-surface-variant block">{{ __('Waktu Check-in:') }}</span>
                                <p class="font-bold text-on-surface mt-0.5">{{ now()->format('H:i:s') }} WIB</p>
                            </div>
                        </div>

                        <!-- Barcode Simulation -->
                        <div class="p-3 rounded-xl bg-surface-container-low text-center flex flex-col items-center">
                            <div class="font-mono text-center tracking-widest text-xs font-bold text-brand-navy">
                                |||| || | |||| || ||| | ||||
                            </div>
                            <span class="text-[10px] font-mono text-outline mt-1">{{ $verifiedAppointment->booking_code }}</span>
                        </div>

                        <!-- Ticket Action Buttons -->
                        <div class="pt-2 flex flex-col gap-2.5">
                            <button onclick="window.print()" type="button" class="w-full py-3 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container shadow-sm flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">print</span>
                                <span>{{ __('Cetak Struk Tiket Antrean') }}</span>
                            </button>
                            <a href="{{ route('queue.index', ['ticketCode' => $issuedTicket->display_number]) }}" class="w-full py-2.5 rounded-xl bg-surface-container text-xs font-bold text-primary hover:bg-surface-container-high flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">phone_android</span>
                                <span>{{ __('Pantau di Ponsel Anda') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Placeholder / Ready State -->
                <div class="rounded-3xl bg-surface-card p-8 text-center border border-dashed border-outline-variant/40 shadow-sm flex flex-col items-center justify-center min-h-[380px]">
                    <div class="w-16 h-16 rounded-full bg-surface-container-low text-outline flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-[36px]">confirmation_number</span>
                    </div>
                    <h3 class="font-heading text-sm font-bold text-on-surface mb-1">{{ __('Menunggu Data Check-in') }}</h3>
                    <p class="text-xs text-on-surface-variant max-w-xs leading-relaxed">
                        {{ __('Tiket fisik antrean poliklinik beserta nomor ruang dokter akan muncul di sini setelah Anda memverifikasi kode booking atau NIK.') }}
                    </p>
                </div>
            @endif

            <!-- Help Desk Assistance Card -->
            <div class="p-5 rounded-2xl bg-surface-card border border-outline-variant/30 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-brand-gold/15 text-brand-navy flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">help_center</span>
                </div>
                <div>
                    <h4 class="font-heading text-xs font-bold text-on-surface">{{ __('Kendala Check-in Mandiri?') }}</h4>
                    <p class="text-[11px] text-on-surface-variant mt-0.5">
                        {{ __('Silakan hubungi petugas admission lobi di Loket 1 untuk bantuan manual.') }}
                    </p>
                </div>
            </div>

        </div>

    </div>
</div>
