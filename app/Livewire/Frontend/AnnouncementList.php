<?php

namespace App\Livewire\Frontend;

use App\Models\Announcement;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.frontend')]
class AnnouncementList extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Announcement::published()->latest('published_at');

        if (! empty($this->search)) {
            $search = '%'.$this->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('title->id', 'like', $search)
                    ->orWhere('title->en', 'like', $search)
                    ->orWhere('content->id', 'like', $search)
                    ->orWhere('content->en', 'like', $search)
                    ->orWhere('summary->id', 'like', $search)
                    ->orWhere('summary->en', 'like', $search);
            });
        }

        $announcements = $query->paginate(6);
        $featuredAnnouncement = Announcement::published()->latest('published_at')->first();

        return view('livewire.frontend.announcement-list', [
            'announcements' => $announcements,
            'featuredAnnouncement' => $featuredAnnouncement,
        ]);
    }
}
