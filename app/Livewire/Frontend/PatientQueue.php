<?php

namespace App\Livewire\Frontend;

use App\Enums\QueueTicketStatus;
use App\Models\Doctor;
use App\Models\QueueTicket;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.frontend')]
class PatientQueue extends Component
{
    #[Url]
    public string $ticketCode = '';

    public ?int $activeTicketId = null;

    public function mount(): void
    {
        if (! empty($this->ticketCode)) {
            $this->searchTicket();
        } elseif (Auth::guard('patient')->check()) {
            $patient = Auth::guard('patient')->user();
            // Look for today's active ticket
            $ticket = QueueTicket::whereHas('appointment', function ($q) use ($patient) {
                $q->where('patient_id', $patient->id);
            })->latest()->first();

            if ($ticket) {
                $this->activeTicketId = $ticket->id;
                $this->ticketCode = $ticket->display_number;
            }
        }

        // If still no ticket selected, pick the first active ticket in the system as a demonstration
        if (! $this->activeTicketId) {
            $demoTicket = QueueTicket::latest()->first();
            if ($demoTicket) {
                $this->activeTicketId = $demoTicket->id;
                $this->ticketCode = $demoTicket->display_number;
            }
        }
    }

    public function searchTicket(): void
    {
        $query = trim($this->ticketCode);
        if (empty($query)) {
            return;
        }

        $ticket = QueueTicket::where('display_number', $query)
            ->orWhereHas('appointment', function ($q) use ($query) {
                $q->where('booking_code', $query)
                    ->orWhereHas('patient', function ($p) use ($query) {
                        $p->where('national_id', $query)
                            ->orWhere('medical_record_number', $query);
                    });
            })->latest()->first();

        if ($ticket) {
            $this->activeTicketId = $ticket->id;
            $this->ticketCode = $ticket->display_number;
            $this->resetErrorBag();
        } else {
            $this->addError('ticketCode', __('Nomor antrean atau kode booking tidak ditemukan.'));
        }
    }

    public function cancelQueue(int $ticketId): void
    {
        $ticket = QueueTicket::find($ticketId);
        if ($ticket) {
            $ticket->update(['status' => QueueTicketStatus::Cancelled]);
            session()->flash('success', __('Antrean ').$ticket->display_number.__(' telah dibatalkan.'));
        }
    }

    public function render()
    {
        $activeTicket = $this->activeTicketId ? QueueTicket::with(['appointment.patient', 'doctor', 'schedule'])->find($this->activeTicketId) : null;

        // Currently serving ticket for the same doctor or poliklinik
        $currentlyServingTicket = null;
        $remainingBefore = 0;

        if ($activeTicket) {
            $currentlyServingTicket = QueueTicket::where('doctor_id', $activeTicket->doctor_id)
                ->where('status', QueueTicketStatus::Serving)
                ->first();

            if (! $currentlyServingTicket) {
                // If none currently serving, pick the highest completed or first waiting
                $currentlyServingTicket = QueueTicket::where('doctor_id', $activeTicket->doctor_id)
                    ->where('id', '<', $activeTicket->id)
                    ->whereIn('status', [QueueTicketStatus::Serving, QueueTicketStatus::Completed])
                    ->latest()
                    ->first();
            }

            // Count patients waiting before this ticket
            $remainingBefore = QueueTicket::where('doctor_id', $activeTicket->doctor_id)
                ->where('status', QueueTicketStatus::Waiting)
                ->where('id', '<', $activeTicket->id)
                ->count();
        }

        // Summary of all clinics today
        $clinicsSummary = [
            [
                'name' => __('Poli Penyakit Dalam'),
                'room' => 'Ruang 204 • Lt. 2',
                'current' => 'A-009',
                'waiting' => 5,
                'status' => 'active',
            ],
            [
                'name' => __('Poli Anak (Pediatri)'),
                'room' => 'Ruang 103 • Lt. 1',
                'current' => 'B-004',
                'waiting' => 3,
                'status' => 'active',
            ],
            [
                'name' => __('Poli Gigi & Mulut'),
                'room' => 'Ruang 201 • Lt. 2',
                'current' => 'C-007',
                'waiting' => 6,
                'status' => 'active',
            ],
            [
                'name' => __('Instalasi Farmasi & Obat'),
                'room' => 'Loket Farmasi Utama',
                'current' => 'F-028',
                'waiting' => 8,
                'status' => 'active',
            ],
        ];

        return view('livewire.frontend.patient-queue', [
            'activeTicket' => $activeTicket,
            'currentlyServingTicket' => $currentlyServingTicket,
            'remainingBefore' => $remainingBefore,
            'clinicsSummary' => $clinicsSummary,
        ]);
    }
}
