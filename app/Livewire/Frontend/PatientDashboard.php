<?php

namespace App\Livewire\Frontend;

use App\Enums\AppointmentStatus;
use App\Enums\CheckInMethod;
use App\Enums\QueueTicketPriority;
use App\Enums\QueueTicketStatus;
use App\Models\Appointment;
use App\Models\QueueTicket;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.frontend')]
class PatientDashboard extends Component
{
    public string $appointmentTab = 'upcoming';

    public function selfCheckIn(int $appointmentId): void
    {
        $patient = Auth::guard('patient')->user();
        $appointment = Appointment::with(['doctor', 'schedule'])
            ->where('patient_id', $patient->id)
            ->where('id', $appointmentId)
            ->firstOrFail();

        // Only allow check-in for confirmed appointments on today's date
        if ($appointment->status !== AppointmentStatus::Confirmed) {
            session()->flash('error', __('This appointment cannot be checked in. Current status: ').$appointment->status->label());

            return;
        }

        if (! $appointment->appointment_date->isToday()) {
            session()->flash('error', __('Self check-in is only available on the appointment date.'));

            return;
        }

        // Generate queue ticket
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

        QueueTicket::create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $appointment->doctor_id,
            'schedule_id' => $appointment->schedule_id,
            'queue_date' => $today,
            'queue_number' => $nextNumber,
            'prefix' => $prefix,
            'display_number' => $displayNumber,
            'status' => QueueTicketStatus::Waiting,
            'priority' => QueueTicketPriority::Normal,
            'counter' => 'Ruang Poli '.($prefix === 'A' ? '204' : ($prefix === 'B' ? '102' : '201')),
            'call_count' => 0,
        ]);

        $appointment->update([
            'status' => AppointmentStatus::CheckedIn,
            'checked_in_at' => now(),
            'check_in_method' => CheckInMethod::SelfService,
        ]);

        session()->flash('success', __('Check-in successful! Your queue ticket: ').$displayNumber);
    }

    public function cancelAppointment(int $appointmentId): void
    {
        $patient = Auth::guard('patient')->user();
        $appointment = Appointment::where('patient_id', $patient->id)
            ->where('id', $appointmentId)
            ->firstOrFail();

        if (! in_array($appointment->status, [AppointmentStatus::Pending, AppointmentStatus::Confirmed], true)) {
            session()->flash('error', __('This appointment can no longer be cancelled.'));

            return;
        }

        $appointment->update([
            'status' => AppointmentStatus::Cancelled,
            'cancellation_reason' => __('Cancelled by patient from dashboard'),
            'cancelled_at' => now(),
        ]);

        session()->flash('success', __('Appointment ').$appointment->booking_code.__(' has been cancelled.'));
    }

    public function render()
    {
        $patient = Auth::guard('patient')->user();

        // Active queue tickets today
        $activeTickets = QueueTicket::whereHas('appointment', function ($q) use ($patient) {
            $q->where('patient_id', $patient->id);
        })
            ->whereIn('status', [QueueTicketStatus::Waiting, QueueTicketStatus::Serving])
            ->where('queue_date', now()->format('Y-m-d'))
            ->with(['doctor', 'appointment', 'schedule'])
            ->get();

        // Upcoming appointments (confirmed, pending, checked_in — future or today)
        $upcomingAppointments = Appointment::where('patient_id', $patient->id)
            ->whereIn('status', [
                AppointmentStatus::Pending,
                AppointmentStatus::Confirmed,
                AppointmentStatus::CheckedIn,
                AppointmentStatus::InProgress,
            ])
            ->where('appointment_date', '>=', now()->startOfDay())
            ->with(['doctor', 'schedule', 'queueTicket'])
            ->orderBy('appointment_date')
            ->get();

        // Past appointments (completed, cancelled, no_show, or past dates)
        $pastAppointments = Appointment::where('patient_id', $patient->id)
            ->where(function ($q) {
                $q->whereIn('status', [
                    AppointmentStatus::Completed,
                    AppointmentStatus::Cancelled,
                    AppointmentStatus::NoShow,
                ])->orWhere('appointment_date', '<', now()->startOfDay());
            })
            ->with(['doctor', 'schedule', 'queueTicket'])
            ->orderByDesc('appointment_date')
            ->take(20)
            ->get();

        return view('livewire.frontend.patient-dashboard', [
            'patient' => $patient,
            'activeTickets' => $activeTickets,
            'upcomingAppointments' => $upcomingAppointments,
            'pastAppointments' => $pastAppointments,
        ]);
    }
}
