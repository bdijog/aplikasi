<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Announcement extends Model
{
    use HasTranslations;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Announcement $announcement) {
            if (empty($announcement->slug)) {
                $title = $announcement->getTranslation('title', 'id', false);

                if (empty($title)) {
                    $translations = $announcement->getTranslations('title');
                    $title = $translations['id'] ?? $translations['en'] ?? (is_array($translations) && ! empty($translations) ? reset($translations) : null) ?: $announcement->title;
                }

                if (! empty($title)) {
                    $baseSlug = Str::slug($title);
                    $slug = $baseSlug;
                    $counter = 1;

                    while (static::where('slug', $slug)->exists()) {
                        $slug = "{$baseSlug}-{$counter}";
                        $counter++;
                    }

                    $announcement->slug = $slug;
                }
            }
        });
    }

    /**
     * The attributes that are translatable.
     * These will be stored as JSON in the database.
     *
     * @var list<string>
     */
    public array $translatable = ['title', 'content', 'summary'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'summary',
        'slug',
        'is_active',
        'published_at',
        'expired_at',
        'image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active'    => 'boolean',
            'published_at' => 'datetime',
            'expired_at'   => 'datetime',
        ];
    }

    /**
     * Scope: only published (active and within schedule).
     */
    public function scopePublished($query): void
    {
        $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            });
    }

    /**
     * Check if the announcement is currently active and visible.
     */
    public function isVisible(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->published_at && $this->published_at->isFuture()) {
            return false;
        }

        if ($this->expired_at && $this->expired_at->isPast()) {
            return false;
        }

        return true;
    }
}
