<?php

namespace App\Livewire\Frontend;

use App\Enums\ScheduleStatus;
use App\Models\Announcement;
use App\Models\Doctor;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.frontend')]
class HomePage extends Component
{
    public string $quickTicketCode = '';

    public function checkQuickTicket(): void
    {
        $code = trim($this->quickTicketCode);
        if (! empty($code)) {
            $this->redirect(route('queue.index', ['ticketCode' => $code]));
        }
    }

    public function render()
    {
        // Featured doctors
        $featuredDoctors = Doctor::where('is_active', true)
            ->with(['schedules' => function ($q) {
                $q->where('status', ScheduleStatus::Active);
            }])
            ->take(4)
            ->get();

        // Latest announcements
        $latestAnnouncements = Announcement::published()
            ->latest('published_at')
            ->take(3)
            ->get();

        // Sample active queue counters
        $activeQueues = [
            ['name' => __('Internal Medicine Clinic'), 'number' => 'A-009', 'room' => 'Ruang 204'],
            ['name' => __('Pediatric Clinic'), 'number' => 'B-004', 'room' => 'Ruang 103'],
            ['name' => __('Dental & Oral Clinic'), 'number' => 'C-007', 'room' => 'Ruang 201'],
            ['name' => __('Pharmacy Facility'), 'number' => 'F-028', 'room' => 'Loket Obat'],
        ];

        return view('livewire.frontend.home-page', [
            'featuredDoctors' => $featuredDoctors,
            'latestAnnouncements' => $latestAnnouncements,
            'activeQueues' => $activeQueues,
        ]);
    }
}
