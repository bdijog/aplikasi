<?php

namespace App\Livewire\Frontend;

use App\Enums\QueueTicketStatus;
use App\Models\QueueTicket;
use App\Models\ServiceCounter;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.queue-display')]
class QueueDisplay extends Component
{
    public ?int $lastCalledTicketId = null;

    public function render()
    {
        // Fetch current active / serving ticket
        $currentCalled = QueueTicket::with(['doctor', 'appointment.patient'])
            ->whereIn('status', [QueueTicketStatus::Serving, QueueTicketStatus::Waiting])
            ->latest('called_at')
            ->first();

        // If no called_at, get latest updated
        if (! $currentCalled) {
            $currentCalled = QueueTicket::with(['doctor', 'appointment.patient'])->latest('updated_at')->first();
        }

        // Recent called tickets
        $recentCalled = QueueTicket::with(['doctor', 'appointment.patient'])
            ->whereIn('status', [QueueTicketStatus::Serving, QueueTicketStatus::Completed])
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Counters list
        $counters = ServiceCounter::where('is_active', true)->get()->map(function ($counter) {
            // Find latest ticket for this counter or random mock
            $ticket = QueueTicket::where('counter', $counter->name)
                ->orWhere('counter', 'like', '%'.$counter->code.'%')
                ->latest()
                ->first();

            return [
                'counter' => $counter,
                'ticket' => $ticket,
            ];
        });

        // If no service counters in database, provide fallback polyclinics
        if ($counters->isEmpty()) {
            $counters = collect([
                [
                    'counter' => (object) ['name' => 'Poli Penyakit Dalam', 'code' => 'POLI-A', 'location' => 'Ruang 204 Lt. 2'],
                    'ticket' => (object) ['display_number' => 'A-009', 'status' => (object) ['value' => 'serving']],
                ],
                [
                    'counter' => (object) ['name' => 'Poli Anak & Tumbuh Kembang', 'code' => 'POLI-B', 'location' => 'Ruang 102 Lt. 1'],
                    'ticket' => (object) ['display_number' => 'B-004', 'status' => (object) ['value' => 'serving']],
                ],
                [
                    'counter' => (object) ['name' => 'Poli Gigi & Mulut', 'code' => 'POLI-C', 'location' => 'Ruang 201 Lt. 2'],
                    'ticket' => (object) ['display_number' => 'C-007', 'status' => (object) ['value' => 'serving']],
                ],
                [
                    'counter' => (object) ['name' => 'Poli Jantung & Pembuluh', 'code' => 'POLI-D', 'location' => 'Ruang 205 Lt. 2'],
                    'ticket' => (object) ['display_number' => 'D-003', 'status' => (object) ['value' => 'serving']],
                ],
                [
                    'counter' => (object) ['name' => 'Instalasi Farmasi & Obat', 'code' => 'FARMASI', 'location' => 'Lantai 1'],
                    'ticket' => (object) ['display_number' => 'F-028', 'status' => (object) ['value' => 'serving']],
                ],
                [
                    'counter' => (object) ['name' => 'Loket Pendaftaran & Admisi', 'code' => 'ADMISI', 'location' => 'Lobi Utama'],
                    'ticket' => (object) ['display_number' => 'ADM-15', 'status' => (object) ['value' => 'serving']],
                ],
            ]);
        }

        return view('livewire.frontend.queue-display', [
            'currentCalled' => $currentCalled,
            'recentCalled' => $recentCalled,
            'counters' => $counters,
        ]);
    }
}
