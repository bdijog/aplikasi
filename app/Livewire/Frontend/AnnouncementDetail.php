<?php

namespace App\Livewire\Frontend;

use App\Models\Announcement;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.frontend')]
class AnnouncementDetail extends Component
{
    public Announcement $announcement;

    public function mount(string $slug): void
    {
        if (is_numeric($slug)) {
            $this->announcement = Announcement::where('id', (int) $slug)
                ->orWhere('slug', $slug)
                ->firstOrFail();
        } else {
            $this->announcement = Announcement::where('slug', $slug)->firstOrFail();
        }
    }

    public function render()
    {
        $relatedAnnouncements = Announcement::published()
            ->where('id', '!=', $this->announcement->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('livewire.frontend.announcement-detail', [
            'relatedAnnouncements' => $relatedAnnouncements,
        ]);
    }
}
