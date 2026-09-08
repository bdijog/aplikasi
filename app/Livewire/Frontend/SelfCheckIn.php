<?php

namespace App\Livewire\Frontend;

use App\Enums\AppointmentStatus;
use App\Enums\CheckInMethod;
use App\Enums\QueueTicketPriority;
use App\Enums\QueueTicketStatus;
use App\Models\Appointment;
use App\Models\QueueTicket;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.frontend')]
class SelfCheckIn extends Component
{
    #[Url]
    public string $bookingCode = '';

    public string $activeTab = 'manual'; // 'manual' or 'scan'

    public ?Appointment $verifiedAppointment = null;

    public ?QueueTicket $issuedTicket = null;

    public string $errorMessage = '';

    public function mount(): void
    {
        if (! empty($this->bookingCode)) {
            $this->verifyCheckIn();
        } else {
            // Find a sample appointment for demonstration ease
            $sample = Appointment::where('status', AppointmentStatus::Confirmed)->latest()->first();
            if ($sample) {
                $this->bookingCode = $sample->booking_code;
            }
        }
    }

    public function fillSample(string $code): void
    {
        $this->bookingCode = $code;
        $this->errorMessage = '';
    }

    public function verifyCheckIn(): void
    {
        $this->errorMessage = '';
        $this->verifiedAppointment = null;
        $this->issuedTicket = null;

        $code = trim($this->bookingCode);
        if (empty($code)) {
            $this->errorMessage = __('Please enter your booking code, NIK, or medical record number.');

            return;
        }

        // Search appointment
        $appointment = Appointment::with(['patient', 'doctor', 'schedule'])
            ->where('booking_code', $code)
            ->orWhereHas('patient', function ($q) use ($code) {
                $q->where('national_id', $code)
                    ->orWhere('medical_record_number', $code);
            })
            ->latest()
            ->first();

        if (! $appointment) {
            $this->errorMessage = __('Reservation not found. Please ensure you have booked and entered the code correctly.');

            return;
        }

        // Check if ticket already exists
        $ticket = QueueTicket::where('appointment_id', $appointment->id)->first();

        if (! $ticket) {
            // Determine prefix from doctor specialty
            $spec = $appointment->doctor?->getTranslation('specialty', 'id') ?? 'Umum';
            $prefix = match (true) {
                str_contains($spec, 'Dalam') => 'A',
                str_contains($spec, 'Anak') => 'B',
                str_contains($spec, 'Gigi') => 'C',
                str_contains($spec, 'Jantung') => 'D',
                str_contains($spec, 'Mata') => 'E',
                default => 'U',
            };

            $today = now()->format('Y-m-d');
            $latestQueueNumber = QueueTicket::where('doctor_id', $appointment->doctor_id)
                ->where('queue_date', $today)
                ->max('queue_number') ?: 0;

            $nextNumber = $latestQueueNumber + 1;
            $displayNumber = $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

            $ticket = QueueTicket::create([
                'appointment_id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'schedule_id' => $appointment->schedule_id,
                'queue_date' => $today,
                'queue_number' => $nextNumber,
                'prefix' => $prefix,
                'display_number' => $displayNumber,
                'status' => QueueTicketStatus::Waiting,
                'priority' => QueueTicketPriority::Regular,
                'counter' => 'Ruang Poli '.($prefix === 'A' ? '204' : ($prefix === 'B' ? '102' : '201')),
                'call_count' => 0,
            ]);

            // Update appointment status to CheckedIn
            $appointment->update([
                'status' => AppointmentStatus::CheckedIn,
                'checked_in_at' => now(),
                'check_in_method' => CheckInMethod::Kiosk,
            ]);
        }

        $this->verifiedAppointment = $appointment;
        $this->issuedTicket = $ticket;
    }

    public function simulateScanSuccess(): void
    {
        $appointment = Appointment::where('status', AppointmentStatus::Confirmed)->latest()->first();
        if ($appointment) {
            $this->bookingCode = $appointment->booking_code;
            $this->verifyCheckIn();
        }
    }

    public function render()
    {
        return view('livewire.frontend.self-check-in');
    }
}
