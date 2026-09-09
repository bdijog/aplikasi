<div class="max-w-[75rem] mx-auto px-4 md:px-6 py-8">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant font-medium mb-6">
        <a class="hover:text-primary transition-colors" href="{{ route('home') }}">{{ __('Home') }}</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-bold">{{ __('Patient Dashboard') }}</span>
    </nav>

    {{-- Section 1: Patient Profile Header --}}
    <div class="bg-surface-card rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                {{-- Avatar --}}
                <div class="w-16 h-16 rounded-2xl bg-primary/15 text-primary font-bold flex items-center justify-center text-xl font-heading shrink-0">
                    {{ strtoupper(substr($patient->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="font-heading text-xl md:text-2xl font-bold text-on-surface">
                        {{ __('Welcome, :name', ['name' => $patient->name]) }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 mt-1.5">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-status-available bg-status-available/10 px-2.5 py-1 rounded-full">
                            <span class="material-symbols-outlined text-[14px]">verified</span>
                            {{ __('Registered Patient') }}
                        </span>
                        @if($patient->medical_record_number)
                            <span class="text-xs text-on-surface-variant font-medium">
                                {{ __('MRN: ') }}{{ $patient->medical_record_number }}
                            </span>
                        @endif
                        @if($patient->national_id)
                            <span class="text-xs text-on-surface-variant">
                                {{ __('NIK') }}: {{ substr($patient->national_id, 0, 6) }}••••••{{ substr($patient->national_id, -4) }}
                            </span>
                        @endif
                        @if($patient->phone)
                            <span class="text-xs text-on-surface-variant">
                                {{ $patient->phone }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('booking.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-on-primary font-bold text-xs shadow hover:bg-primary-container transition-all">
                    <span class="material-symbols-outlined text-[18px]">event</span>
                    <span>{{ __('Book New Appointment') }}</span>
                </a>
                <a href="{{ route('checkin.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container text-primary font-bold text-xs border border-outline-variant/30 hover:bg-surface-container-high transition-all">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    <span>{{ __('Self Check-in') }}</span>
                </a>
                <form method="POST" action="{{ route('patient.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-error bg-error-container/10 font-bold text-xs hover:bg-error-container/20 transition-all border border-error/20">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                        <span>{{ __('Sign Out') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Section 2: Active Queue Today --}}
    @if($activeTickets->isNotEmpty())
        <div class="mb-8">
            <h2 class="font-heading text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[22px] text-status-available">confirmation_number</span>
                {{ __("Today's Active Queue") }}
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-status-available/10 text-status-available text-[11px] font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-status-available animate-pulse"></span>
                    {{ __('Live') }}
                </span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($activeTickets as $ticket)
                    <div class="bg-surface-card rounded-2xl p-6 shadow-sm border border-status-available/30 relative overflow-hidden">
                        {{-- Status Indicator --}}
                        <div class="absolute top-0 right-0 w-24 h-24 bg-status-available/5 rounded-bl-[3rem]"></div>

                        <div class="flex items-start justify-between mb-4">
                            {{-- Queue Number (Big) --}}
                            <div class="text-center">
                                <div class="text-4xl font-heading font-bold text-primary leading-none">
                                    {{ $ticket->display_number }}
                                </div>
                                <p class="text-[11px] text-on-surface-variant mt-1 font-medium">{{ __('Queue Number') }}</p>
                            </div>
                            {{-- Status Badge --}}
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold {{ $ticket->status === \App\Enums\QueueTicketStatus::Serving ? 'bg-primary/15 text-primary' : 'bg-status-limited/15 text-status-limited' }}">
                                <span class="w-1.5 h-1.5 rounded-full animate-pulse {{ $ticket->status === \App\Enums\QueueTicketStatus::Serving ? 'bg-primary' : 'bg-status-limited' }}"></span>
                                {{ $ticket->status === \App\Enums\QueueTicketStatus::Serving ? __('Being Called') : __('Waiting') }}
                            </span>
                        </div>

                        {{-- Doctor & Room Info --}}
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-surface-container-low mb-3">
                            <span class="material-symbols-outlined text-[24px] text-primary">stethoscope</span>
                            <div>
                                <p class="text-sm font-bold text-on-surface">{{ $ticket->doctor?->name ?? '-' }}</p>
                                <p class="text-xs text-on-surface-variant">{{ $ticket->counter ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <a href="{{ route('queue.index', ['ticketCode' => $ticket->display_number]) }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-primary/10 text-primary font-bold text-xs hover:bg-primary/20 transition-all">
                            <span class="material-symbols-outlined text-[18px]">monitor</span>
                            <span>{{ __('Monitor Live Queue') }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Section 3: My Appointments --}}
    <div class="mb-8">
        <h2 class="font-heading text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[22px] text-primary">calendar_month</span>
            {{ __('My Appointments') }}
        </h2>

        {{-- Tab Navigation --}}
        <div class="flex items-center gap-1 p-1 bg-surface-container rounded-xl mb-6 w-full max-w-md">
            <button wire:click="$set('appointmentTab', 'upcoming')" type="button" class="flex-1 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $appointmentTab === 'upcoming' ? 'bg-surface-card text-primary shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}">
                <span class="material-symbols-outlined text-[18px]">upcoming</span>
                <span>{{ __('Upcoming') }} ({{ $upcomingAppointments->count() }})</span>
            </button>
            <button wire:click="$set('appointmentTab', 'history')" type="button" class="flex-1 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $appointmentTab === 'history' ? 'bg-surface-card text-primary shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}">
                <span class="material-symbols-outlined text-[18px]">history</span>
                <span>{{ __('Visit History') }} ({{ $pastAppointments->count() }})</span>
            </button>
        </div>

        {{-- Appointment Cards --}}
        @php
            $appointments = $appointmentTab === 'upcoming' ? $upcomingAppointments : $pastAppointments;
        @endphp

        @if($appointments->isEmpty())
            <div class="bg-surface-card rounded-2xl p-10 text-center shadow-sm border border-outline-variant/30">
                <span class="material-symbols-outlined text-[48px] text-outline mb-3">event_busy</span>
                <h3 class="font-heading text-lg font-bold text-on-surface mb-1">
                    {{ $appointmentTab === 'upcoming' ? __('No Upcoming Appointments') : __('No Visit History') }}
                </h3>
                <p class="text-sm text-on-surface-variant mb-4">
                    {{ $appointmentTab === 'upcoming' ? __('You have no scheduled appointments. Book one now!') : __('Your past visit records will appear here.') }}
                </p>
                @if($appointmentTab === 'upcoming')
                    <a href="{{ route('booking.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-on-primary font-bold text-xs shadow hover:bg-primary-container transition-all">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        <span>{{ __('Book Appointment') }}</span>
                    </a>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @foreach($appointments as $appointment)
                    <div class="bg-surface-card rounded-2xl p-5 md:p-6 shadow-sm border border-outline-variant/30 hover:shadow-md transition-shadow" wire:key="appointment-{{ $appointment->id }}">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            {{-- Left: Doctor & Booking Info --}}
                            <div class="flex items-start gap-4 flex-1 min-w-0">
                                {{-- Doctor Avatar --}}
                                <div class="w-12 h-12 rounded-xl bg-primary-container/30 text-primary font-bold flex items-center justify-center text-sm shrink-0">
                                    @if($appointment->doctor?->photo)
                                        <img src="{{ asset('storage/'.$appointment->doctor->photo) }}" alt="{{ $appointment->doctor->name }}" class="w-full h-full object-cover rounded-xl"/>
                                    @else
                                        <span class="material-symbols-outlined text-[24px]">person</span>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    {{-- Booking Code --}}
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[11px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-md font-mono">
                                            {{ $appointment->booking_code }}
                                        </span>
                                        {{-- Status Badge --}}
                                        @php
                                            $statusColor = match($appointment->status) {
                                                \App\Enums\AppointmentStatus::Confirmed => 'bg-status-available/10 text-status-available',
                                                \App\Enums\AppointmentStatus::CheckedIn => 'bg-primary/10 text-primary',
                                                \App\Enums\AppointmentStatus::InProgress => 'bg-secondary/10 text-secondary',
                                                \App\Enums\AppointmentStatus::Completed => 'bg-outline/10 text-outline',
                                                \App\Enums\AppointmentStatus::Cancelled => 'bg-error/10 text-error',
                                                \App\Enums\AppointmentStatus::NoShow => 'bg-on-surface-variant/10 text-on-surface-variant',
                                                default => 'bg-status-limited/10 text-status-limited',
                                            };
                                        @endphp
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-md {{ $statusColor }}">
                                            {{ $appointment->status->label() }}
                                        </span>
                                    </div>

                                    {{-- Doctor Name --}}
                                    <p class="text-sm font-bold text-on-surface truncate">
                                        {{ $appointment->doctor?->name ?? '-' }}
                                    </p>
                                    <p class="text-xs text-on-surface-variant truncate">
                                        {{ $appointment->doctor?->getTranslation('specialty', app()->getLocale()) ?? '-' }}
                                        @if($appointment->schedule)
                                            • {{ $appointment->schedule->counter ?? '' }}
                                        @endif
                                    </p>

                                    {{-- Date & Session --}}
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-on-surface-variant">
                                        <span class="inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                            {{ $appointment->appointment_date->isoFormat('dddd, D MMMM Y') }}
                                        </span>
                                        @if($appointment->estimated_time)
                                            <span class="inline-flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                                {{ \Carbon\Carbon::parse($appointment->estimated_time)->format('H:i') }} WIB
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Chief Complaint --}}
                                    @if($appointment->chief_complaint)
                                        <p class="text-xs text-on-surface-variant mt-1.5 truncate max-w-sm">
                                            {{ __('Chief Complaint') }}: {{ \Illuminate\Support\Str::limit($appointment->chief_complaint, 60) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Right: Actions --}}
                            <div class="flex flex-wrap items-center gap-2 shrink-0">
                                {{-- Self Check-in (only for confirmed appointments today) --}}
                                @if($appointment->status === \App\Enums\AppointmentStatus::Confirmed && $appointment->appointment_date->isToday())
                                    <button
                                        wire:click="selfCheckIn({{ $appointment->id }})"
                                        wire:confirm="{{ __('Confirm check-in for this appointment?') }}"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-status-available text-white font-bold text-xs shadow hover:bg-status-available/90 transition-all"
                                    >
                                        <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
                                        <span>{{ __('Check-in Now') }}</span>
                                    </button>
                                @endif

                                {{-- View Queue Ticket (if checked in) --}}
                                @if($appointment->queueTicket)
                                    <a href="{{ route('queue.index', ['ticketCode' => $appointment->queueTicket->display_number]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-primary/10 text-primary font-bold text-xs hover:bg-primary/20 transition-all">
                                        <span class="material-symbols-outlined text-[16px]">confirmation_number</span>
                                        <span>{{ $appointment->queueTicket->display_number }}</span>
                                    </a>
                                @endif

                                {{-- Cancel Appointment --}}
                                @if(in_array($appointment->status, [\App\Enums\AppointmentStatus::Pending, \App\Enums\AppointmentStatus::Confirmed], true))
                                    <button
                                        wire:click="cancelAppointment({{ $appointment->id }})"
                                        wire:confirm="{{ __('Are you sure you want to cancel this appointment? This action cannot be undone.') }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-error bg-error-container/10 font-bold text-xs hover:bg-error-container/20 transition-all border border-error/20"
                                    >
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                        <span>{{ __('Cancel') }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Section 4: Help & Information Widget --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Clinic Guide --}}
        <div class="bg-surface-card rounded-2xl p-5 shadow-sm border border-outline-variant/30">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px] text-primary">menu_book</span>
                </div>
                <h3 class="font-heading text-sm font-bold text-on-surface">{{ __('Patient Guide') }}</h3>
            </div>
            <p class="text-xs text-on-surface-variant leading-relaxed mb-3">
                {{ __('Please arrive 15 minutes before session time to perform ') }}Self Check-in{{ __(' at the lobby kiosk machine.') }}
            </p>
            <a href="{{ route('checkin.index') }}" class="text-xs font-bold text-primary hover:text-primary-container transition-colors inline-flex items-center gap-1">
                <span>{{ __('Self Check-in Guide') }}</span>
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
        </div>

        {{-- WhatsApp Admission --}}
        <div class="bg-surface-card rounded-2xl p-5 shadow-sm border border-outline-variant/30">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-status-available/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px] text-status-available">chat</span>
                </div>
                <h3 class="font-heading text-sm font-bold text-on-surface">{{ __('Need Help?') }}</h3>
            </div>
            <p class="text-xs text-on-surface-variant leading-relaxed mb-3">
                {{ __('Our admission team is ready to assist you regarding BPJS referrals, private insurance, or specialist appointments via WhatsApp.') }}
            </p>
            <a href="https://wa.me/628123456789" target="_blank" class="text-xs font-bold text-status-available hover:text-status-available/80 transition-colors inline-flex items-center gap-1">
                <span>{{ __('Chat with Admission Officer on WhatsApp') }}</span>
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
        </div>

        {{-- Announcements --}}
        <div class="bg-surface-card rounded-2xl p-5 shadow-sm border border-outline-variant/30">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-brand-gold/15 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px] text-brand-gold">campaign</span>
                </div>
                <h3 class="font-heading text-sm font-bold text-on-surface">{{ __('Announcements') }}</h3>
            </div>
            <p class="text-xs text-on-surface-variant leading-relaxed mb-3">
                {{ __('Get the latest information regarding holiday operational schedules, immunization programs, community health education, and clinic system updates.') }}
            </p>
            <a href="{{ route('announcements.index') }}" class="text-xs font-bold text-primary hover:text-primary-container transition-colors inline-flex items-center gap-1">
                <span>{{ __('View All Updates') }}</span>
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
        </div>
    </div>
</div>
