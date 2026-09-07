<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Models\Announcement;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateAnnouncement extends CreateRecord
{
    use Translatable;

    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug'])) {
            $titleId = null;

            // Prioritize Indonesian title ('id')
            if ($this->activeLocale === 'id' && ! empty($data['title'])) {
                $titleId = $data['title'];
            } elseif (! empty($this->otherLocaleData['id']['title'])) {
                $titleId = $this->otherLocaleData['id']['title'];
            } elseif (! empty($data['title'])) {
                $titleId = $data['title'];
            }

            if ($titleId) {
                $baseSlug = Str::slug($titleId);
                $slug = $baseSlug;
                $counter = 1;

                while (Announcement::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $data['slug'] = $slug;
            }
        }

        return $data;
    }
}
