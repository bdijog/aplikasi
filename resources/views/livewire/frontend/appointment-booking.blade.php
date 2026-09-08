<div class="max-w-[75rem] mx-auto px-4 md:px-6 py-8">
    <!-- Breadcrumb & Step Navigation Container -->
    <div class="mb-8">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant font-medium mb-3">
            <a class="hover:text-primary transition-colors" href="{{ route('home') }}">{{ __('Beranda') }}</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a class="hover:text-primary transition-colors" href="{{ route('doctors.index') }}">{{ __('Jadwal Dokter') }}</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">{{ __('Booking Janji Temu') }}</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl md:text-3xl font-bold text-on-surface">
                    {{ __('Pendaftaran Pasien & Booking Janji Temu') }}
                </h1>
                <p class="text-xs text-on-surface-variant mt-1">
                    {{ __('Sistem registrasi terpadu Klinik Ayo Sehat dengan estimasi nomor antrean otomatis.') }}
                </p>
            </div>
            
            <div class="flex items-center gap-2 bg-surface-container-low px-3 py-1.5 rounded-full border border-outline-variant/30 text-xs font-semibold text-primary">
                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                <span>{{ __('Sistem Terenkripsi & Rekam Medis Standar Kemenkes') }}</span>
            </div>
        </div>

        <!-- Wizard Stepper Indicators -->
        <div class="mt-8 bg-surface-card rounded-2xl p-4 shadow-sm border border-outline-variant/30">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                <!-- Step 1 -->
                <div class="flex items-center gap-2.5 p-2 rounded-xl {{ $step === 1 ? 'bg-primary/10 text-primary font-bold' : ($step > 1 ? 'text-status-available' : 'text-outline') }}">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 {{ $step === 1 ? 'bg-primary text-white' : ($step > 1 ? 'bg-status-available text-white' : 'bg-surface-container text-outline') }}">
                        @if($step > 1)
                            <span class="material-symbols-outlined text-[16px]">check</span>
                        @else
                            1
                        @endif
                    </div>
                    <span class="text-xs truncate">{{ __('1. Data Pasien') }}</span>
                </div>

                <!-- Step 2 -->
                <div class="flex items-center gap-2.5 p-2 rounded-xl {{ $step === 2 ? 'bg-primary/10 text-primary font-bold' : ($step > 2 ? 'text-status-available' : 'text-outline') }}">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 {{ $step === 2 ? 'bg-primary text-white' : ($step > 2 ? 'bg-status-available text-white' : 'bg-surface-container text-outline') }}">
                        @if($step > 2)
                            <span class="material-symbols-outlined text-[16px]">check</span>
                        @else
                            2
                        @endif
                    </div>
                    <span class="text-xs truncate">{{ __('2. Pilih Dokter') }}</span>
                </div>

                <!-- Step 3 -->
                <div class="flex items-center gap-2.5 p-2 rounded-xl {{ $step === 3 ? 'bg-primary/10 text-primary font-bold' : ($step > 3 ? 'text-status-available' : 'text-outline') }}">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 {{ $step === 3 ? 'bg-primary text-white' : ($step > 3 ? 'bg-status-available text-white' : 'bg-surface-container text-outline') }}">
                        @if($step > 3)
                            <span class="material-symbols-outlined text-[16px]">check</span>
                        @else
                            3
                        @endif
                    </div>
                    <span class="text-xs truncate">{{ __('3. Tanggal & Sesi') }}</span>
                </div>

                <!-- Step 4 -->
                <div class="flex items-center gap-2.5 p-2 rounded-xl {{ $step >= 4 ? 'bg-primary/10 text-primary font-bold' : 'text-outline' }}">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 {{ $step >= 4 ? 'bg-primary text-white' : 'bg-surface-container text-outline' }}">
                        @if($step === 5)
                            <span class="material-symbols-outlined text-[16px]">check</span>
                        @else
                            4
                        @endif
                    </div>
                    <span class="text-xs truncate">{{ __('4. Konfirmasi') }}</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-surface-container h-1.5 rounded-full mt-3 overflow-hidden">
                <div class="bg-primary h-full rounded-full transition-all duration-300" style="width: {{ match($step) { 1 => '25%', 2 => '50%', 3 => '75%', 4 => '90%', 5 => '100%', default => '25%' } }};"></div>
            </div>
        </div>
    </div>

    <!-- Main Grid: 8 Columns Form & 4 Columns Sticky Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Step Forms (Span 8) -->
        <div class="lg:col-span-8 flex flex-col gap-6">

            <!-- STEP 1: PATIENT IDENTITY -->
            @if($step === 1)
                <div class="bg-surface-card rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 animate-in fade-in duration-200">
                    <div class="flex items-center justify-between pb-4 border-b border-outline-variant/20 mb-6">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-primary text-[26px]">badge</span>
                            <h2 class="font-heading text-lg font-bold text-on-surface">{{ __('Langkah 1: Identitas & Akun Pasien') }}</h2>
                        </div>
                        <span class="text-[11px] px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-medium">{{ __('Wajib Diisi') }}</span>
                    </div>

                    @if(auth('patient')->check())
                        <!-- Logged In Patient Summary -->
                        <div class="bg-surface-container-low rounded-2xl p-6 border border-outline-variant/20">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-bold text-status-available flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-status-available"></span>
                                    {{ __('Akun Pasien Terautentikasi') }}
                                </span>
                                <span class="text-xs font-bold text-primary">{{ __('No. RM: ') }}{{ auth('patient')->user()->medical_record_number }}</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <div>
                                    <span class="text-on-surface-variant">{{ __('Nama Lengkap:') }}</span>
                                    <p class="font-bold text-sm text-on-surface mt-0.5">{{ auth('patient')->user()->name }}</p>
                                </div>
                                <div>
                                    <span class="text-on-surface-variant">{{ __('Nomor Induk Kependudukan (NIK):') }}</span>
                                    <p class="font-bold text-sm text-on-surface mt-0.5">{{ auth('patient')->user()->national_id ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-on-surface-variant">{{ __('Nomor WhatsApp / HP:') }}</span>
                                    <p class="font-bold text-sm text-on-surface mt-0.5">{{ auth('patient')->user()->phone }}</p>
                                </div>
                                <div>
                                    <span class="text-on-surface-variant">{{ __('Tanggal Lahir & Gender:') }}</span>
                                    <p class="font-bold text-sm text-on-surface mt-0.5">
                                        {{ auth('patient')->user()->date_of_birth ? auth('patient')->user()->date_of_birth->format('d/m/Y') : '-' }} • 
                                        {{ auth('patient')->user()->gender?->label() ?? '-' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-outline-variant/20 flex flex-col sm:flex-row items-center justify-between gap-3">
                                <form method="POST" action="{{ route('patient.logout') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-error hover:underline flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">logout</span>
                                        <span>{{ __('Bukan Anda? Ganti Akun Pasien') }}</span>
                                    </button>
                                </form>

                                <button wire:click="continueAsLoggedInPatient" type="button" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container shadow-sm flex items-center justify-center gap-2">
                                    <span>{{ __('Lanjut ke Pemilihan Dokter') }}</span>
                                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Guest Mode: Tabs for New vs Existing -->
                        <div class="flex items-center p-1 bg-surface-container rounded-xl mb-6">
                            <button wire:click="selectPatientMode('new')" type="button" class="flex-1 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 {{ $patientMode === 'new' ? 'bg-surface-card text-primary shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}">
                                <span class="material-symbols-outlined text-[18px]">person_add</span>
                                <span>{{ __('Pasien Baru (Daftar Akun)') }}</span>
                            </button>
                            <button wire:click="selectPatientMode('existing')" type="button" class="flex-1 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 {{ $patientMode === 'existing' ? 'bg-surface-card text-primary shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}">
                                <span class="material-symbols-outlined text-[18px]">login</span>
                                <span>{{ __('Pasien Lama (Masuk Akun)') }}</span>
                            </button>
                        </div>

                        @if($patientMode === 'new')
                            <!-- Form Registrasi Pasien Baru -->
                            <form wire:submit="registerAndProceed" class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                            {{ __('Nomor Induk Kependudukan (NIK) *') }}
                                        </label>
                                        <input wire:model="national_id" type="text" maxlength="16" placeholder="{{ __('16 digit angka KTP') }}" class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"/>
                                        @error('national_id') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                            {{ __('Nama Lengkap (Sesuai KTP) *') }}
                                        </label>
                                        <input wire:model="name" type="text" placeholder="{{ __('Nama lengkap pasien') }}" class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"/>
                                        @error('name') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                            {{ __('Tanggal Lahir *') }}
                                        </label>
                                        <input wire:model="date_of_birth" type="date" class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"/>
                                        @error('date_of_birth') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                            {{ __('Jenis Kelamin *') }}
                                        </label>
                                        <select wire:model="gender" class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none">
                                            <option value="male">{{ __('Laki-laki') }}</option>
                                            <option value="female">{{ __('Perempuan') }}</option>
                                        </select>
                                        @error('gender') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                            {{ __('Nomor WhatsApp / HP *') }}
                                        </label>
                                        <input wire:model="phone" type="tel" placeholder="{{ __('Contoh: 081234567890') }}" class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"/>
                                        @error('phone') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                            {{ __('Email (Opsional)') }}
                                        </label>
                                        <input wire:model="email" type="email" placeholder="{{ __('alamat@email.com') }}" class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"/>
                                        @error('email') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                            {{ __('Buat Kata Sandi Akun Pasien *') }}
                                        </label>
                                        <input wire:model="password" type="password" placeholder="{{ __('Minimal 6 karakter') }}" class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"/>
                                        @error('password') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                            {{ __('Golongan Darah') }}
                                        </label>
                                        <select wire:model="blood_type" class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none">
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="AB">AB</option>
                                            <option value="O">O</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                        {{ __('Alamat Lengkap Domisili') }}
                                    </label>
                                    <textarea wire:model="address" rows="2" placeholder="{{ __('Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten') }}" class="w-full px-4 py-2 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"></textarea>
                                </div>

                                <div class="pt-4 flex justify-end">
                                    <button type="submit" class="px-8 py-3 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container shadow-md flex items-center gap-2">
                                        <span>{{ __('Daftar & Lanjut ke Pilih Dokter') }}</span>
                                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                    </button>
                                </div>
                            </form>
                        @else
                            <!-- Form Login Pasien Lama -->
                            <form wire:submit="loginAndProceed" class="space-y-4 max-w-md mx-auto py-4">
                                <div>
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                        {{ __('NIK, Nomor Rekam Medis (RM), atau Email') }}
                                    </label>
                                    <input wire:model="login_identifier" type="text" placeholder="{{ __('Contoh: 320123... atau RM-2026-...') }}" class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"/>
                                    @error('login_identifier') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                        {{ __('Kata Sandi Akun Pasien') }}
                                    </label>
                                    <input wire:model="login_password" type="password" placeholder="{{ __('Masukkan kata sandi') }}" class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"/>
                                    @error('login_password') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="pt-4">
                                    <button type="submit" class="w-full py-3 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container shadow-md flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-[18px]">login</span>
                                        <span>{{ __('Masuk & Lanjut Booking') }}</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    @endif
                </div>
            @endif

            <!-- STEP 2: DOCTOR SELECTION -->
            @if($step === 2)
                <div class="bg-surface-card rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 animate-in fade-in duration-200">
                    <div class="flex items-center justify-between pb-4 border-b border-outline-variant/20 mb-6">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-primary text-[26px]">stethoscope</span>
                            <h2 class="font-heading text-lg font-bold text-on-surface">{{ __('Langkah 2: Pilih Dokter & Spesialis') }}</h2>
                        </div>
                        <button wire:click="goToStep(1)" type="button" class="text-xs text-on-surface-variant hover:text-primary flex items-center gap-1 font-semibold">
                            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                            <span>{{ __('Ubah Pasien') }}</span>
                        </button>
                    </div>

                    <!-- Specialty Filter Pills -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-4 scrollbar-none">
                        <button wire:click="$set('specialtyFilter', 'all')" type="button" class="px-3.5 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all {{ $specialtyFilter === 'all' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                            {{ __('Semua Poliklinik') }}
                        </button>
                        <button wire:click="$set('specialtyFilter', 'Penyakit Dalam')" type="button" class="px-3.5 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all {{ $specialtyFilter === 'Penyakit Dalam' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                            {{ __('Penyakit Dalam') }}
                        </button>
                        <button wire:click="$set('specialtyFilter', 'Anak')" type="button" class="px-3.5 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all {{ $specialtyFilter === 'Anak' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                            {{ __('Anak (Pediatri)') }}
                        </button>
                        <button wire:click="$set('specialtyFilter', 'Obstetri')" type="button" class="px-3.5 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all {{ $specialtyFilter === 'Obstetri' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                            {{ __('Kandungan (Obgyn)') }}
                        </button>
                        <button wire:click="$set('specialtyFilter', 'Jantung')" type="button" class="px-3.5 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all {{ $specialtyFilter === 'Jantung' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                            {{ __('Jantung') }}
                        </button>
                        <button wire:click="$set('specialtyFilter', 'Mata')" type="button" class="px-3.5 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all {{ $specialtyFilter === 'Mata' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                            {{ __('Mata') }}
                        </button>
                    </div>

                    <!-- Doctors Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($availableDoctors as $doc)
                            <div wire:click="selectDoctor({{ $doc->id }})" class="p-4 rounded-2xl border cursor-pointer transition-all flex items-start gap-3.5 {{ $selectedDoctorId === $doc->id ? 'bg-primary/10 border-primary ring-2 ring-primary/40' : 'bg-surface-container-low border-outline-variant/30 hover:bg-surface-container hover:border-primary/40' }}">
                                <div class="w-14 h-14 rounded-xl bg-primary/20 text-primary flex items-center justify-center font-heading font-bold text-lg shrink-0 overflow-hidden">
                                    @if($doc->photo)
                                        <img class="w-full h-full object-cover" src="{{ asset('storage/' . $doc->photo) }}" alt="{{ $doc->name }}"/>
                                    @else
                                        {{ strtoupper(substr($doc->name, 0, 2)) }}
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="text-[10px] font-bold text-primary px-2 py-0.5 rounded bg-primary/10">
                                        {{ $doc->getTranslation('specialty', app()->getLocale()) }}
                                    </span>
                                    <h4 class="font-heading text-sm font-bold text-on-surface truncate mt-1">{{ $doc->name }}</h4>
                                    <p class="text-[11px] text-on-surface-variant mt-0.5">STR: {{ $doc->license_number ?? '-' }}</p>
                                    <div class="mt-2 text-[10px] text-status-available font-bold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-status-available"></span>
                                        <span>{{ count($doc->schedules) }} {{ __('Jadwal Praktik Aktif') }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="w-6 h-6 rounded-full border flex items-center justify-center {{ $selectedDoctorId === $doc->id ? 'bg-primary border-primary text-white' : 'border-outline-variant' }}">
                                        @if($selectedDoctorId === $doc->id)
                                            <span class="material-symbols-outlined text-[16px]">check</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- STEP 3: DATE & SESSION SELECTION -->
            @if($step === 3)
                <div class="bg-surface-card rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 animate-in fade-in duration-200">
                    <div class="flex items-center justify-between pb-4 border-b border-outline-variant/20 mb-6">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-primary text-[26px]">calendar_month</span>
                            <h2 class="font-heading text-lg font-bold text-on-surface">{{ __('Langkah 3: Pilih Tanggal & Sesi Kunjungan') }}</h2>
                        </div>
                        <button wire:click="goToStep(2)" type="button" class="text-xs text-on-surface-variant hover:text-primary flex items-center gap-1 font-semibold">
                            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                            <span>{{ __('Ganti Dokter') }}</span>
                        </button>
                    </div>

                    @if(count($upcomingScheduleSlots) === 0)
                        <div class="p-8 bg-surface-container-low rounded-2xl text-center">
                            <span class="material-symbols-outlined text-outline text-[40px] mb-2">event_busy</span>
                            <p class="text-xs text-on-surface-variant">{{ __('Dokter ini belum memiliki jadwal praktik dalam 14 hari ke depan.') }}</p>
                            <button wire:click="goToStep(2)" class="mt-3 px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl">{{ __('Pilih Dokter Lain') }}</button>
                        </div>
                    @else
                        <!-- Mini Interactive Calendar Strip -->
                        <div class="mb-6">
                            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-3">
                                {{ __('Pilih Hari Praktik Tersedia') }}
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-2.5">
                                @foreach($upcomingScheduleSlots as $slot)
                                    <button wire:click="selectSchedule({{ $slot['schedule']->id }}, '{{ $slot['date'] }}')" type="button" class="flex flex-col items-center justify-center p-3 rounded-xl border transition-all text-center {{ $selectedDate === $slot['date'] ? 'bg-primary text-white border-primary shadow-md scale-102' : ($slot['isFull'] ? 'bg-surface-container/50 border-outline-variant/20 opacity-50 cursor-not-allowed' : 'bg-surface-container-low border-outline-variant/30 hover:bg-surface-container text-on-surface') }}">
                                        <span class="text-[10px] uppercase font-bold {{ $selectedDate === $slot['date'] ? 'text-white/80' : 'text-on-surface-variant' }}">{{ $slot['dayAbbr'] }}</span>
                                        <span class="text-lg font-bold font-heading my-0.5">{{ $slot['dayNumber'] }}</span>
                                        <span class="text-[10px] {{ $selectedDate === $slot['date'] ? 'text-white/80' : 'text-on-surface-variant' }}">{{ $slot['monthAbbr'] }}</span>
                                        
                                        @if($slot['isFull'])
                                            <span class="text-[9px] font-bold text-status-full mt-1">{{ __('Penuh') }}</span>
                                        @elseif($slot['remaining'] <= 3)
                                            <span class="text-[9px] font-bold {{ $selectedDate === $slot['date'] ? 'text-white' : 'text-status-limited' }} mt-1">{{ __('Sisa ') }}{{ $slot['remaining'] }}</span>
                                        @else
                                            <span class="text-[9px] font-bold {{ $selectedDate === $slot['date'] ? 'text-white' : 'text-status-available' }} mt-1">{{ __('Tersedia') }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Time Slot Choices -->
                        <div class="mb-6">
                            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-3">
                                {{ __('Slot Waktu Kunjungan (Estimasi 15-20 menit)') }}
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @php
                                    $timeSlots = ['08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '13:30', '14:00'];
                                @endphp
                                @foreach($timeSlots as $slotTime)
                                    <label class="p-3 rounded-xl border cursor-pointer transition-all flex flex-col justify-between {{ $selectedTimeSlot === $slotTime ? 'bg-primary/10 border-primary ring-2 ring-primary/40' : 'bg-surface-container-low border-outline-variant/30 hover:bg-surface-container' }}">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-bold font-heading {{ $selectedTimeSlot === $slotTime ? 'text-primary' : 'text-on-surface' }}">{{ $slotTime }} WIB</span>
                                            <input wire:model.live="selectedTimeSlot" type="radio" value="{{ $slotTime }}" class="accent-primary w-4 h-4"/>
                                        </div>
                                        <span class="text-[10px] text-status-available font-medium flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-status-available"></span>
                                            <span>{{ __('Kuota Siap') }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-4 flex justify-between items-center">
                            <button wire:click="goToStep(2)" type="button" class="px-5 py-2.5 rounded-xl bg-surface-container text-xs font-bold text-on-surface hover:bg-surface-container-high">
                                {{ __('Kembali') }}
                            </button>
                            <button wire:click="goToStep(4)" type="button" class="px-8 py-3 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container shadow-md flex items-center gap-2">
                                <span>{{ __('Lanjut ke Keluhan & Penjamin') }}</span>
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- STEP 4: COMPLAINT & INSURANCE / PAYMENT -->
            @if($step === 4)
                <div class="bg-surface-card rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 animate-in fade-in duration-200">
                    <div class="flex items-center justify-between pb-4 border-b border-outline-variant/20 mb-6">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-primary text-[26px]">medical_information</span>
                            <h2 class="font-heading text-lg font-bold text-on-surface">{{ __('Langkah 4: Keluhan Medis & Penjamin') }}</h2>
                        </div>
                        <button wire:click="goToStep(3)" type="button" class="text-xs text-on-surface-variant hover:text-primary flex items-center gap-1 font-semibold">
                            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                            <span>{{ __('Ubah Waktu') }}</span>
                        </button>
                    </div>

                    <form wire:submit="confirmBooking" class="space-y-5">
                        <!-- Jenis Kunjungan -->
                        <div>
                            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-2">
                                {{ __('Jenis Kunjungan *') }}
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="p-3 rounded-xl border cursor-pointer flex items-center gap-3 {{ $visit_type === 'new_visit' ? 'bg-primary/10 border-primary font-bold text-primary' : 'bg-surface-container-low border-outline-variant/30 text-on-surface' }}">
                                    <input wire:model.live="visit_type" type="radio" value="new_visit" class="accent-primary"/>
                                    <span class="text-xs">{{ __('Kunjungan Baru') }}</span>
                                </label>
                                <label class="p-3 rounded-xl border cursor-pointer flex items-center gap-3 {{ $visit_type === 'follow_up' ? 'bg-primary/10 border-primary font-bold text-primary' : 'bg-surface-container-low border-outline-variant/30 text-on-surface' }}">
                                    <input wire:model.live="visit_type" type="radio" value="follow_up" class="accent-primary"/>
                                    <span class="text-xs">{{ __('Kontrol / Kunjungan Ulang') }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Keluhan Utama -->
                        <div>
                            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                {{ __('Keluhan Utama / Gejala yang Dirasakan *') }}
                            </label>
                            <textarea wire:model="chief_complaint" rows="3" placeholder="{{ __('Deskripsikan singkat gejala, keluhan fisik, atau tujuan periksa dokter...') }}" class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"></textarea>
                            @error('chief_complaint') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Metode Pembayaran / Penjamin -->
                        <div>
                            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-2">
                                {{ __('Pilihan Penjamin Medis / Pembayaran *') }}
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label class="p-3 rounded-xl border cursor-pointer flex flex-col justify-between {{ $payment_method === 'mandiri' ? 'bg-primary/10 border-primary font-bold text-primary' : 'bg-surface-container-low border-outline-variant/30 text-on-surface' }}">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-bold">{{ __('Pasien Umum / Mandiri') }}</span>
                                        <input wire:model.live="payment_method" type="radio" value="mandiri" class="accent-primary"/>
                                    </div>
                                    <span class="text-[11px] text-on-surface-variant font-normal">{{ __('Kasir, QRIS, Tunai, Kartu Debit/Kredit') }}</span>
                                </label>

                                <label class="p-3 rounded-xl border cursor-pointer flex flex-col justify-between {{ $payment_method === 'bpjs' ? 'bg-primary/10 border-primary font-bold text-primary' : 'bg-surface-container-low border-outline-variant/30 text-on-surface' }}">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-bold">{{ __('BPJS Kesehatan') }}</span>
                                        <input wire:model.live="payment_method" type="radio" value="bpjs" class="accent-primary"/>
                                    </div>
                                    <span class="text-[11px] text-on-surface-variant font-normal">{{ __('Rujukan Faskes 1 aktif / Mobile JKN') }}</span>
                                </label>

                                <label class="p-3 rounded-xl border cursor-pointer flex flex-col justify-between {{ $payment_method === 'swasta' ? 'bg-primary/10 border-primary font-bold text-primary' : 'bg-surface-container-low border-outline-variant/30 text-on-surface' }}">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-bold">{{ __('Asuransi Swasta') }}</span>
                                        <input wire:model.live="payment_method" type="radio" value="swasta" class="accent-primary"/>
                                    </div>
                                    <span class="text-[11px] text-on-surface-variant font-normal">{{ __('AdMedika, Prudential, Astra, dll') }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- No Kartu jika BPJS atau Asuransi -->
                        @if($payment_method !== 'mandiri')
                            <div>
                                <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                                    {{ $payment_method === 'bpjs' ? __('Nomor Kartu BPJS Kesehatan (13 Digit)') : __('Nama Perusahaan Asuransi & Nomor Polis') }}
                                </label>
                                <input wire:model="insurance_number" type="text" placeholder="{{ $payment_method === 'bpjs' ? '0001234567890' : 'Contoh: Prudential - 98765432' }}" class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-sm border border-outline-variant/30 focus:bg-surface-card focus:ring-2 focus:ring-primary focus:outline-none"/>
                            </div>
                        @endif

                        <!-- Terms & Conditions Agreement -->
                        <div class="p-4 rounded-xl bg-surface-container-low flex items-start gap-3 border border-outline-variant/20">
                            <input wire:model="terms_agreed" type="checkbox" id="terms_agree" class="accent-primary w-4 h-4 rounded mt-0.5 cursor-pointer"/>
                            <label for="terms_agree" class="text-xs text-on-surface leading-relaxed cursor-pointer">
                                {{ __('Saya menyetujui seluruh ') }}<span class="font-bold text-primary">{{ __('Tata Tertib Kunjungan Pasien Rawat Jalan') }}</span>{{ __(' Klinik Ayo Sehat serta memahami bahwa konfirmasi kehadiran (Self Check-in) dilakukan paling lambat 15 menit sebelum jam praktik berlangsung.') }}
                            </label>
                        </div>
                        @error('terms_agreed') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror

                        <!-- Action Buttons -->
                        <div class="pt-4 flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                            <button wire:click="goToStep(3)" type="button" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-surface-container text-xs font-bold text-on-surface hover:bg-surface-container-high">
                                {{ __('Kembali ke Jadwal') }}
                            </button>
                            <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container shadow-md flex items-center justify-center gap-2">
                                <span>{{ __('Konfirmasi Booking Sekarang') }}</span>
                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- STEP 5: SUCCESS CONFIRMATION -->
            @if($step === 5 && $confirmedAppointment)
                <div class="bg-surface-card rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 animate-in fade-in duration-200">
                    <div class="text-center max-w-lg mx-auto mb-8">
                        <div class="w-16 h-16 rounded-full bg-status-available/15 text-status-available flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-[36px]">check_circle</span>
                        </div>
                        <h2 class="font-heading text-2xl font-bold text-on-surface">{{ __('Booking Berhasil Dikonfirmasi!') }}</h2>
                        <p class="text-xs text-on-surface-variant mt-1.5 leading-relaxed">
                            {{ __('Reservasi Anda telah tersimpan di sistem rekam medis. Tunjukkan bukti barcode ini kepada petugas atau scan pada anjungan Self Check-in saat tiba di klinik.') }}
                        </p>
                    </div>

                    <!-- Booking Ticket Visual Card -->
                    <div class="bg-surface-container-low rounded-2xl p-6 border-2 border-dashed border-outline-variant/40 relative overflow-hidden mb-6">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-6 border-b border-outline-variant/20">
                            <div>
                                <span class="text-[11px] font-bold text-on-surface-variant uppercase tracking-wider">{{ __('Kode Booking Pasien') }}</span>
                                <div class="text-2xl md:text-3xl font-heading font-black text-primary tracking-wider mt-0.5">
                                    {{ $confirmedAppointment->booking_code }}
                                </div>
                                <span class="text-[11px] text-status-available font-semibold flex items-center gap-1 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-status-available"></span>
                                    {{ __('Status: Terkonfirmasi Aktif') }}
                                </span>
                            </div>

                            <!-- Barcode / QR Simulation -->
                            <div class="flex flex-col items-center bg-white p-3 rounded-xl shadow-xs border border-outline-variant/20">
                                <div class="font-mono text-center tracking-widest text-xs font-bold text-brand-navy">
                                    ||| | |||| | ||| |||| | ||
                                </div>
                                <span class="text-[10px] font-mono text-outline mt-1">{{ $confirmedAppointment->booking_code }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-6 text-xs">
                            <div>
                                <span class="text-on-surface-variant">{{ __('Nama Pasien:') }}</span>
                                <p class="font-bold text-on-surface mt-0.5">{{ $confirmedAppointment->patient->name }}</p>
                                <p class="text-[10px] text-outline">{{ __('RM: ') }}{{ $confirmedAppointment->patient->medical_record_number }}</p>
                            </div>
                            <div>
                                <span class="text-on-surface-variant">{{ __('Dokter & Poliklinik:') }}</span>
                                <p class="font-bold text-on-surface mt-0.5">{{ $confirmedAppointment->doctor->name }}</p>
                                <p class="text-[10px] text-primary font-semibold">{{ $confirmedAppointment->doctor->getTranslation('specialty', app()->getLocale()) }}</p>
                            </div>
                            <div>
                                <span class="text-on-surface-variant">{{ __('Tanggal & Sesi Kunjungan:') }}</span>
                                <p class="font-bold text-on-surface mt-0.5">{{ $confirmedAppointment->appointment_date->isoFormat('dddd, D MMMM Y') }}</p>
                                <p class="text-[10px] text-primary font-semibold">{{ substr($confirmedAppointment->estimated_time, 0, 5) }} WIB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Next Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        <button onclick="window.print()" type="button" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-surface-container text-xs font-bold text-on-surface hover:bg-surface-container-high flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">print</span>
                            <span>{{ __('Cetak Bukti Booking') }}</span>
                        </button>
                        <a href="{{ route('queue.index') }}" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container shadow-sm flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
                            <span>{{ __('Lihat Status Antrean Saya') }}</span>
                        </a>
                        <button wire:click="resetBookingFlow" type="button" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-surface-container text-xs font-bold text-primary hover:bg-surface-container-high">
                            {{ __('Buat Booking Baru') }}
                        </button>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Column: Sticky Summary & Doctor Context (Span 4) -->
        <aside class="lg:col-span-4 flex flex-col gap-6 lg:sticky lg:top-28">
            <!-- Summary Card -->
            <div class="bg-surface-card rounded-2xl p-6 shadow-md border border-outline-variant/30 flex flex-col gap-4">
                <div class="flex items-center justify-between pb-2 border-b border-outline-variant/20">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary">{{ __('Ringkasan Janji Temu') }}</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-container text-[11px] font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px]">bolt</span>
                        <span>{{ __('Konfirmasi Instan') }}</span>
                    </span>
                </div>

                <!-- Doctor Profile Snippet -->
                @if($selectedDoctor)
                    <div class="flex items-center gap-3.5 p-3 rounded-xl bg-surface-container-low">
                        <div class="w-14 h-14 rounded-xl bg-primary/20 text-primary flex items-center justify-center font-heading font-bold text-base shrink-0 overflow-hidden">
                            @if($selectedDoctor->photo)
                                <img class="w-full h-full object-cover" src="{{ asset('storage/' . $selectedDoctor->photo) }}" alt="{{ $selectedDoctor->name }}"/>
                            @else
                                {{ strtoupper(substr($selectedDoctor->name, 0, 2)) }}
                            @endif
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-heading text-xs font-bold text-on-surface truncate">{{ $selectedDoctor->name }}</span>
                            <span class="text-[11px] text-primary font-semibold truncate">{{ $selectedDoctor->getTranslation('specialty', app()->getLocale()) }}</span>
                            <div class="flex items-center gap-1 mt-1 text-on-surface-variant text-[11px]">
                                <span class="material-symbols-outlined text-[15px] text-primary">meeting_room</span>
                                <span>{{ __('Poli ') }}{{ $selectedDoctor->getTranslation('specialty', app()->getLocale()) }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-surface-container-low text-center text-xs text-on-surface-variant">
                        {{ __('Silakan pilih dokter spesialis pada langkah 2') }}
                    </div>
                @endif

                <!-- Estimated Queue Number Card -->
                <div class="bg-brand-navy text-surface p-4 rounded-xl flex items-center justify-between shadow-sm">
                    <div class="flex flex-col">
                        <span class="text-[10px] text-secondary-fixed uppercase tracking-wider font-bold">{{ __('Estimasi No. Antrean') }}</span>
                        <span class="font-heading text-2xl font-bold text-white">#{{ str_pad((string) $estimatedQueueNumber, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-[10px] text-surface-container-high">{{ __('Sesuai urutan check-in kedatangan') }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-brand-gold">
                        <span class="material-symbols-outlined text-[24px]">query_stats</span>
                    </div>
                </div>

                <!-- Booking Details Breakdown -->
                <div class="flex flex-col gap-2.5 text-xs">
                    <div class="flex items-center justify-between py-1 border-b border-outline-variant/15">
                        <span class="text-on-surface-variant flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-outline">calendar_today</span>
                            <span>{{ __('Tanggal') }}</span>
                        </span>
                        <span class="font-bold text-on-surface">
                            {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->isoFormat('dddd, D MMM Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-outline-variant/15">
                        <span class="text-on-surface-variant flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-outline">schedule</span>
                            <span>{{ __('Sesi Kunjungan') }}</span>
                        </span>
                        <span class="font-bold text-primary">{{ $selectedTimeSlot }} WIB</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-outline-variant/15">
                        <span class="text-on-surface-variant flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-outline">security</span>
                            <span>{{ __('Penjamin Medis') }}</span>
                        </span>
                        <span class="font-bold text-status-available">
                            {{ match($payment_method) {
                                'bpjs' => __('BPJS Kesehatan (Cover 100%)'),
                                'swasta' => __('Asuransi Rekanan'),
                                default => __('Pasien Mandiri (Umum)')
                            } }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-on-surface-variant flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-outline">payments</span>
                            <span>{{ __('Biaya Pendaftaran') }}</span>
                        </span>
                        <span class="font-bold text-on-surface">
                            {{ $payment_method === 'bpjs' ? 'Rp 0,-' : 'Rp 25.000,-' }}
                        </span>
                    </div>
                </div>

                <!-- Micro Notice -->
                <div class="p-3 rounded-xl bg-surface-container-low flex items-start gap-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-primary text-[18px] mt-0.5">info</span>
                    <p class="text-[11px] leading-relaxed">
                        {{ __('Mohon hadir 15 menit sebelum waktu sesi untuk melakukan ') }}<strong>{{ __('Self Check-in') }}</strong>{{ __(' di mesin kiosk lobi.') }}
                    </p>
                </div>
            </div>

            <!-- Front Desk WhatsApp Help Desk -->
            <div class="bg-surface-card rounded-2xl p-5 shadow-sm border border-outline-variant/30 flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-status-available/10 text-status-available flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">support_agent</span>
                    </div>
                    <div>
                        <h4 class="font-heading text-xs font-bold text-on-surface">{{ __('Butuh Bantuan Reservasi?') }}</h4>
                        <p class="text-[11px] text-on-surface-variant">{{ __('Front Desk siap memandu Anda') }}</p>
                    </div>
                </div>
                <a href="https://wa.me/6281234567890" target="_blank" class="w-full py-2.5 px-4 rounded-xl bg-surface-container-low hover:bg-surface-container text-status-available text-xs font-bold flex items-center justify-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">chat</span>
                    <span>{{ __('Chat WhatsApp Petugas Admisi') }}</span>
                </a>
            </div>

            <!-- KARS Stamp -->
            <div class="bg-surface-container-low/70 rounded-2xl p-4 flex items-center gap-3 border border-outline-variant/20">
                <span class="material-symbols-outlined text-brand-gold text-[28px]">verified</span>
                <div>
                    <span class="text-xs font-bold text-brand-navy block">{{ __('KARS Paripurna & Kemenkes') }}</span>
                    <span class="text-[11px] text-on-surface-variant">{{ __('Pelayanan rekam medis elektronik terenkripsi aman.') }}</span>
                </div>
            </div>
        </aside>
    </div>
</div>
