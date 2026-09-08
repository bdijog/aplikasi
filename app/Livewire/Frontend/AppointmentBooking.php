<?php

namespace App\Livewire\Frontend;

use App\Enums\AppointmentSource;
use App\Enums\AppointmentStatus;
use App\Enums\Gender;
use App\Enums\ScheduleStatus;
use App\Enums\VisitType;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.frontend')]
class AppointmentBooking extends Component
{
    public int $step = 1;

    // Auth / Patient mode: 'new' or 'existing'
    public string $patientMode = 'new';

    // Step 1: Patient form (New Patient)
    public string $national_id = '';

    public string $name = '';

    public string $date_of_birth = '';

    public string $gender = 'male';

    public string $phone = '';

    public string $email = '';

    public string $password = '';

    public string $address = '';

    public string $blood_type = 'O';

    // Step 1: Existing Patient Login
    public string $login_identifier = ''; // NIK or Email

    public string $login_password = '';

    // Step 2: Doctor Selection
    #[Url]
    public ?int $selectedDoctorId = null;

    public string $specialtyFilter = 'all';

    // Step 3: Date & Session
    #[Url]
    public string $selectedDate = '';

    public ?int $selectedScheduleId = null;

    public string $selectedTimeSlot = '08:30';

    // Step 4: Medical intake & Insurance
    public string $visit_type = 'new_visit';

    public string $chief_complaint = '';

    public string $patient_notes = '';

    public string $payment_method = 'mandiri'; // mandiri, bpjs, swasta

    public string $insurance_number = '';

    public bool $terms_agreed = true;

    // Step 5: Confirmed Booking Result
    public ?Appointment $confirmedAppointment = null;

    public function mount(): void
    {
        if (Auth::guard('patient')->check()) {
            $patient = Auth::guard('patient')->user();
            $this->national_id = $patient->national_id ?? '';
            $this->name = $patient->name ?? '';
            $this->phone = $patient->phone ?? '';
            $this->email = $patient->email ?? '';
            $this->date_of_birth = $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '';
            $this->gender = $patient->gender?->value ?? 'male';
            $this->address = $patient->address ?? '';
            $this->patientMode = 'existing';

            // If doctor_id is passed in URL, jump to step 2 or 3
            if ($this->selectedDoctorId) {
                $this->step = 2;
            }
        }

        if (empty($this->selectedDate)) {
            $this->selectedDate = now()->addDay()->format('Y-m-d');
        }
    }

    public function selectPatientMode(string $mode): void
    {
        $this->patientMode = $mode;
        $this->resetErrorBag();
    }

    public function registerAndProceed(): void
    {
        $this->validate([
            'national_id' => 'required|digits:16|unique:patients,national_id',
            'name' => 'required|string|min:3|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'phone' => 'required|string|min:8|max:20',
            'email' => 'nullable|email|unique:patients,email',
            'password' => 'required|min:6',
            'address' => 'nullable|string|max:500',
        ], [
            'national_id.required' => __('NIK 16 digit wajib diisi.'),
            'national_id.digits' => __('NIK harus tepat 16 digit angka.'),
            'national_id.unique' => __('NIK sudah terdaftar. Silakan pilih tab Pasien Lama untuk masuk.'),
            'name.required' => __('Nama lengkap pasien wajib diisi.'),
            'date_of_birth.required' => __('Tanggal lahir wajib diisi.'),
            'phone.required' => __('Nomor telepon / WhatsApp wajib diisi.'),
            'password.required' => __('Kata sandi akun pasien wajib diisi minimal 6 karakter.'),
        ]);

        // Generate Medical Record Number (RM)
        $latestId = Patient::max('id') + 1;
        $rmNumber = 'RM-'.date('Ym').'-'.str_pad((string) $latestId, 4, '0', STR_PAD_LEFT);

        $patient = Patient::create([
            'medical_record_number' => $rmNumber,
            'national_id' => $this->national_id,
            'name' => $this->name,
            'date_of_birth' => $this->date_of_birth,
            'gender' => Gender::from($this->gender),
            'phone' => $this->phone,
            'email' => $this->email ?: null,
            'password' => Hash::make($this->password),
            'address' => $this->address ?: null,
            'blood_type' => $this->blood_type,
        ]);

        Auth::guard('patient')->login($patient);

        session()->flash('success', __('Registrasi pasien baru berhasil! Silakan pilih dokter dan jadwal.'));
        $this->step = 2;
    }

    public function loginAndProceed(): void
    {
        $this->validate([
            'login_identifier' => 'required',
            'login_password' => 'required',
        ], [
            'login_identifier.required' => __('Masukkan NIK atau Email terdaftar.'),
            'login_password.required' => __('Masukkan kata sandi Anda.'),
        ]);

        $patient = Patient::where('email', $this->login_identifier)
            ->orWhere('national_id', $this->login_identifier)
            ->orWhere('medical_record_number', $this->login_identifier)
            ->first();

        if (! $patient || ! Hash::check($this->login_password, $patient->password)) {
            $this->addError('login_identifier', __('Kredensial tidak cocok dengan data rekam medis kami.'));

            return;
        }

        Auth::guard('patient')->login($patient);

        $this->national_id = $patient->national_id ?? '';
        $this->name = $patient->name ?? '';
        $this->phone = $patient->phone ?? '';
        $this->email = $patient->email ?? '';
        $this->date_of_birth = $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '';
        $this->gender = $patient->gender?->value ?? 'male';

        session()->flash('success', __('Berhasil masuk sebagai ').$patient->name);
        $this->step = 2;
    }

    public function continueAsLoggedInPatient(): void
    {
        $this->step = 2;
    }

    public function selectDoctor(int $doctorId): void
    {
        $this->selectedDoctorId = $doctorId;
        $this->step = 3;
    }

    public function selectSchedule(int $scheduleId, string $date): void
    {
        $this->selectedScheduleId = $scheduleId;
        $this->selectedDate = $date;
    }

    public function goToStep(int $stepNumber): void
    {
        if ($stepNumber === 2 && ! Auth::guard('patient')->check()) {
            $this->step = 1;

            return;
        }
        if ($stepNumber === 3 && ! $this->selectedDoctorId) {
            $this->step = 2;

            return;
        }
        if ($stepNumber === 4 && (! $this->selectedDoctorId || ! $this->selectedScheduleId)) {
            $this->step = 3;

            return;
        }
        $this->step = $stepNumber;
    }

    public function confirmBooking(): void
    {
        if (! Auth::guard('patient')->check()) {
            $this->step = 1;

            return;
        }

        $this->validate([
            'selectedDoctorId' => 'required|exists:doctors,id',
            'selectedScheduleId' => 'required|exists:schedules,id',
            'selectedDate' => 'required|date|after_or_equal:today',
            'chief_complaint' => 'required|string|min:5|max:1000',
            'terms_agreed' => 'accepted',
        ], [
            'chief_complaint.required' => __('Mohon jelaskan keluhan utama atau tujuan konsultasi medis.'),
            'terms_agreed.accepted' => __('Anda harus menyetujui tata tertib kunjungan klinik.'),
        ]);

        $patient = Auth::guard('patient')->user();
        $doctor = Doctor::findOrFail($this->selectedDoctorId);
        $schedule = Schedule::findOrFail($this->selectedScheduleId);

        // Generate unique booking code: BK-YYYYMMDD-XXXX
        do {
            $code = 'BK-'.date('Ymd').'-'.strtoupper(Str::random(4));
        } while (Appointment::where('booking_code', $code)->exists());

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
            'booking_code' => $code,
            'appointment_date' => $this->selectedDate,
            'estimated_time' => $this->selectedTimeSlot.':00',
            'visit_type' => $this->visit_type === 'new_visit' ? VisitType::NewVisit : VisitType::FollowUp,
            'chief_complaint' => $this->chief_complaint,
            'patient_notes' => 'Metode Pembayaran: '.strtoupper($this->payment_method).($this->insurance_number ? ' ('.$this->insurance_number.')' : '').'. '.$this->patient_notes,
            'status' => AppointmentStatus::Confirmed,
            'source' => AppointmentSource::Online,
            'metadata' => [
                'payment_method' => $this->payment_method,
                'insurance_number' => $this->insurance_number,
                'registered_at_ip' => request()->ip(),
            ],
        ]);

        $this->confirmedAppointment = $appointment;
        $this->step = 5; // Success Step
    }

    public function resetBookingFlow(): void
    {
        $this->step = 1;
        $this->selectedDoctorId = null;
        $this->selectedScheduleId = null;
        $this->chief_complaint = '';
        $this->patient_notes = '';
        $this->confirmedAppointment = null;
    }

    public function render()
    {
        $doctorsQuery = Doctor::where('is_active', true)->with('schedules');
        if ($this->specialtyFilter !== 'all') {
            $search = '%'.$this->specialtyFilter.'%';
            $doctorsQuery->where(function ($q) use ($search) {
                $q->where('specialty->id', 'like', $search)
                    ->orWhere('specialty->en', 'like', $search);
            });
        }
        $availableDoctors = $doctorsQuery->get();

        $selectedDoctor = $this->selectedDoctorId ? Doctor::with('schedules')->find($this->selectedDoctorId) : null;

        // Calculate available slots for the selected doctor
        $upcomingScheduleSlots = [];
        if ($selectedDoctor) {
            $startDate = now();
            for ($i = 0; $i < 14; $i++) {
                $dateObj = $startDate->copy()->addDays($i);
                $dayOfWeek = (int) $dateObj->isoFormat('E');

                $sched = $selectedDoctor->schedules
                    ->where('status', ScheduleStatus::Active)
                    ->firstWhere('day_of_week', $dayOfWeek);

                if ($sched) {
                    $bookedCount = Appointment::where('doctor_id', $selectedDoctor->id)
                        ->where('appointment_date', $dateObj->format('Y-m-d'))
                        ->whereNotIn('status', ['cancelled'])
                        ->count();

                    $maxPatients = $sched->max_patients ?: 20;
                    $remaining = max(0, $maxPatients - $bookedCount);

                    $upcomingScheduleSlots[] = [
                        'date' => $dateObj->format('Y-m-d'),
                        'dateFormatted' => $dateObj->isoFormat('dddd, D MMMM Y'),
                        'dayName' => $dateObj->isoFormat('dddd'),
                        'dayAbbr' => substr($dateObj->isoFormat('dddd'), 0, 3),
                        'dayNum' => $dateObj->format('d'),
                        'dayNumber' => $dateObj->format('j'),
                        'monthAbbr' => $dateObj->isoFormat('MMM'),
                        'schedule' => $sched,
                        'remaining' => $remaining,
                        'max' => $maxPatients,
                        'isFull' => $remaining === 0,
                    ];
                }
            }
        }

        // If selectedScheduleId is not set, set default to first available
        if ($this->selectedDoctorId && ! $this->selectedScheduleId && count($upcomingScheduleSlots) > 0) {
            $this->selectedScheduleId = $upcomingScheduleSlots[0]['schedule']->id;
            $this->selectedDate = $upcomingScheduleSlots[0]['date'];
        }

        // Calculate estimated queue position
        $estimatedQueueNumber = 1;
        if ($this->selectedDoctorId && $this->selectedDate) {
            $estimatedQueueNumber = Appointment::where('doctor_id', $this->selectedDoctorId)
                ->where('appointment_date', $this->selectedDate)
                ->count() + 1;
        }

        return view('livewire.frontend.appointment-booking', [
            'availableDoctors' => $availableDoctors,
            'selectedDoctor' => $selectedDoctor,
            'upcomingScheduleSlots' => $upcomingScheduleSlots,
            'estimatedQueueNumber' => $estimatedQueueNumber,
        ]);
    }
}
