<div class="min-h-screen bg-brand-navy text-surface flex flex-col justify-between p-4 md:p-6 select-none" wire:poll.5s x-data="{ 
    soundEnabled: true,
    lastAnnounced: '{{ $currentCalled?->display_number ?? '' }}',
    currentTime: '',
    currentDate: '',
    initClock() {
        const updateTime = () => {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
            this.currentDate = now.toLocaleDateString('{{ app()->getLocale() === 'en' ? 'en-US' : 'id-ID' }}', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        };
        updateTime();
        setInterval(updateTime, 1000);
    },
    toggleSound() {
        this.soundEnabled = !this.soundEnabled;
    },
    toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    },
    playChimeAndSpeak(number, counter) {
        if (!this.soundEnabled) return;

        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const now = ctx.currentTime;
            
            // Note 1 (D5 - 587Hz)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.frequency.value = 587.33;
            gain1.gain.setValueAtTime(0.4, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.6);

            // Note 2 (A5 - 880Hz)
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.frequency.value = 880.00;
            gain2.gain.setValueAtTime(0.4, now + 0.3);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 1.0);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.3);
            osc2.stop(now + 1.0);
        } catch (e) {
            console.log('AudioContext error:', e);
        }

        setTimeout(() => {
            if ('speechSynthesis' in window) {
                const isEn = '{{ app()->getLocale() }}' === 'en';
                const formattedNum = number.replace('-', ' ');
                const text = isEn 
                    ? `Queue number ${formattedNum}, please proceed to ${counter}`
                    : `Nomor antrean ${formattedNum}, silakan menuju ke ${counter}`;
                
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = isEn ? 'en-US' : 'id-ID';
                utterance.rate = 0.85;
                window.speechSynthesis.speak(utterance);
            }
        }, 1000);
    }
}" x-init="initClock()">

    <!-- Top Header Bar -->
    <header class="bg-surface-card/10 backdrop-blur-md rounded-2xl px-6 py-4 border border-white/10 flex flex-wrap items-center justify-between gap-4 mb-6">
        <!-- Clinic Branding -->
        <div class="flex items-center gap-4">
            <div class="p-2 rounded-xl bg-white shadow-md">
                <img src="{{ asset('images/logo.png') }}" alt="Klinik Ayo Sehat" class="h-10 w-auto object-contain"/>
            </div>
            <div>
                <h1 class="font-heading text-2xl md:text-3xl font-black text-white tracking-wide">
                    KLINIK AYO SEHAT
                </h1>
                <p class="text-xs text-secondary-fixed font-semibold tracking-wider uppercase">
                    {{ __('HealthQueue Integrated Queue Monitor') }}
                </p>
            </div>
        </div>

        <!-- TV Controls & Live Clock -->
        <div class="flex items-center gap-4">
            <!-- Audio Toggle & Test Button -->
            <div class="flex items-center gap-2 bg-white/10 rounded-xl p-1.5 border border-white/10">
                <button @click="toggleSound()" type="button" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5" :class="soundEnabled ? 'bg-status-available text-white shadow' : 'bg-white/20 text-white/60'">
                    <span class="material-symbols-outlined text-[18px]" x-text="soundEnabled ? 'volume_up' : 'volume_off'"></span>
                    <span x-text="soundEnabled ? '{{ __('Sound On') }}' : '{{ __('Muted') }}'"></span>
                </button>

                <button @click="playChimeAndSpeak('{{ $currentCalled?->display_number ?? 'A-012' }}', '{{ $currentCalled?->counter ?? 'Poli Penyakit Dalam' }}')" type="button" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white/10 hover:bg-white/20 text-white transition-all flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">play_circle</span>
                    <span>{{ __('Re-announce Call') }}</span>
                </button>
            </div>

            <!-- Fullscreen Button -->
            <button @click="toggleFullscreen()" type="button" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white transition-colors" title="{{ __('Fullscreen') }}">
                <span class="material-symbols-outlined text-[22px]">fullscreen</span>
            </button>

            <!-- Live Clock Display -->
            <div class="text-right pl-3 border-l border-white/15">
                <div class="font-mono text-2xl font-black text-brand-gold tracking-wider" x-text="currentTime"></div>
                <div class="text-[11px] text-surface-container-high font-medium" x-text="currentDate"></div>
            </div>
        </div>
    </header>

    <!-- Main TV Content Grid (8 Cols Left Spotlight / 4 Cols Right Other Counters) -->
    <main class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch mb-6">
        
        <!-- Left Spotlight: Current Called Ticket (Span 7) -->
        <section class="lg:col-span-7 bg-gradient-to-br from-surface-card/15 to-primary-container/20 rounded-3xl p-6 md:p-8 border border-white/15 shadow-2xl flex flex-col justify-between relative overflow-hidden">
            <!-- Ambient Glow -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-primary/30 rounded-full blur-3xl pointer-events-none"></div>

            <div>
                <!-- Spotlight Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-status-available animate-ping"></span>
                        <span class="text-xs md:text-sm font-black uppercase tracking-widest text-brand-gold">{{ __('CURRENT CALL') }}</span>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-status-available text-white text-xs font-bold uppercase tracking-wider shadow-sm">
                        {{ __('Entering Room') }}
                    </span>
                </div>

                <!-- Prominent Calling Number -->
                <div class="bg-black/25 rounded-3xl p-8 text-center my-4 border border-white/10 shadow-inner">
                    <span class="text-xs md:text-sm uppercase tracking-widest text-surface-container-high font-bold block mb-2">{{ __('QUEUE NUMBER') }}</span>
                    <div class="font-heading text-6xl md:text-8xl lg:text-9xl font-black text-brand-gold tracking-tight drop-shadow-[0_4px_16px_rgba(251,186,21,0.4)]">
                        {{ $currentCalled?->display_number ?? 'A-012' }}
                    </div>
                </div>

                <!-- Destination Counter & Room -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-4">
                    <div class="bg-white/10 rounded-2xl p-5 border border-white/10">
                        <span class="text-xs uppercase tracking-wider text-surface-container-high font-semibold block mb-1">{{ __('DESTINATION CLINIC / COUNTER') }}</span>
                        <div class="font-heading text-xl md:text-2xl font-bold text-white">
                            {{ $currentCalled?->counter ?? 'Ruang Poli Penyakit Dalam' }}
                        </div>
                        <span class="text-xs text-secondary-fixed mt-1 block font-medium">{{ __('Main Building • 2nd Floor') }}</span>
                    </div>

                    <div class="bg-white/10 rounded-2xl p-5 border border-white/10">
                        <span class="text-xs uppercase tracking-wider text-surface-container-high font-semibold block mb-1">{{ __('ATTENDING DOCTOR') }}</span>
                        <div class="font-heading text-lg md:text-xl font-bold text-white truncate">
                            {{ $currentCalled?->doctor?->name ?? 'dr. H. Ahmad Fauzi, Sp.PD' }}
                        </div>
                        <span class="text-xs text-brand-gold mt-1 block font-semibold">{{ __('Active Examination') }}</span>
                    </div>
                </div>
            </div>

            <!-- Audio Instruction Banner -->
            <div class="p-4 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-between gap-4 mt-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-brand-gold text-[26px]">hearing</span>
                    <p class="text-xs text-surface-container-high leading-relaxed">
                        {{ __('Patients whose numbers are called please proceed immediately to the respective clinic room. Prepare referral documents if required.') }}
                    </p>
                </div>
                <button @click="playChimeAndSpeak('{{ $currentCalled?->display_number ?? 'A-012' }}', '{{ $currentCalled?->counter ?? 'Poli Penyakit Dalam' }}')" class="px-4 py-2 rounded-xl bg-brand-gold text-brand-navy font-bold text-xs shrink-0 hover:bg-yellow-400 transition-colors shadow">
                    {{ __('Play Audio') }}
                </button>
            </div>
        </section>

        <!-- Right Side: Grid of All Active Polikliniks (Span 5) -->
        <section class="lg:col-span-5 flex flex-col gap-4">
            <div class="bg-surface-card/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 flex items-center justify-between">
                <span class="text-xs font-black uppercase tracking-wider text-surface">{{ __('OTHER CLINICS QUEUE STATUS') }}</span>
                <span class="text-[11px] text-secondary-fixed font-semibold">{{ count($counters) }} {{ __('Counters Active') }}</span>
            </div>

            <!-- Counters Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1 overflow-y-auto max-h-[520px] pr-1">
                @foreach($counters as $item)
                    @php
                        $counter = $item['counter'];
                        $ticket = $item['ticket'];
                    @endphp
                    <div class="bg-surface-card/15 rounded-2xl p-4 border border-white/10 shadow-sm flex flex-col justify-between hover:border-white/30 transition-colors">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <h3 class="font-heading text-xs font-bold text-white line-clamp-1">
                                    {{ is_string($counter->name) ? $counter->name : ($counter->name[app()->getLocale()] ?? reset($counter->name)) }}
                                </h3>
                                <span class="text-[10px] text-surface-container-high block">
                                    {{ is_string($counter->location) ? $counter->location : ($counter->location[app()->getLocale()] ?? '') }}
                                </span>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-status-available"></span>
                        </div>

                        <div class="bg-black/30 rounded-xl p-3 text-center my-1 border border-white/5">
                            <span class="text-[9px] uppercase tracking-wider text-surface-container-high block">{{ __('CALLED NUMBER') }}</span>
                            <div class="font-heading text-2xl md:text-3xl font-black text-brand-gold tracking-tight">
                                {{ $ticket?->display_number ?? '-' }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-[10px] text-surface-container-high pt-1">
                            <span>{{ __('Status:') }} <strong class="text-status-available">{{ __('Serving') }}</strong></span>
                            <button @click="playChimeAndSpeak('{{ $ticket?->display_number ?? '' }}', '{{ is_string($counter->name) ? $counter->name : '' }}')" type="button" class="text-secondary-fixed hover:underline flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-[14px]">volume_up</span>
                                <span>{{ __('Call') }}</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

    </main>

    <!-- Bottom Running Marquee / Ticker -->
    <footer class="bg-surface-card/10 backdrop-blur-md rounded-2xl px-6 py-3 border border-white/10 flex items-center gap-4 overflow-hidden">
        <div class="flex items-center gap-2 text-brand-gold font-bold text-xs shrink-0">
            <span class="material-symbols-outlined text-[20px] animate-pulse">campaign</span>
            <span class="uppercase tracking-wider">{{ __('CLINIC NOTICE:') }}</span>
        </div>

        <div class="overflow-hidden whitespace-nowrap flex-1 text-xs text-surface-container-high">
            <div class="inline-block animate-marquee">
                {{ __('Welcome to Klinik Ayo Sehat • Please pay attention to your queue number on the display monitor • BPJS Health patients please prepare primary clinic referral letter and ID card • ER and Emergency Procedure Room Open 24 Hours Daily • For reservation assistance contact Admission WhatsApp at 0812-3456-7890 •') }}
            </div>
        </div>

        <div class="shrink-0 text-right text-[11px] text-surface-container-high hidden md:block">
            <span>{{ __('Call Center: (0274) 555-123') }}</span>
        </div>
    </footer>

</div>

<style>
    @keyframes marquee {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
    .animate-marquee {
        display: inline-block;
        animation: marquee 30s linear infinite;
    }
</style>
