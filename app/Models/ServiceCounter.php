<?php

namespace App\Models;

use Database\Factories\ServiceCounterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ServiceCounter extends Model
{
    /** @use HasFactory<ServiceCounterFactory> */
    use HasFactory, HasTranslations;

    /**
     * The attributes that are translatable.
     *
     * @var list<string>
     */
    public array $translatable = ['name', 'location'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'location',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
