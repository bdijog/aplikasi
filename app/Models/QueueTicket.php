<?php

namespace App\Models;

use App\Enums\QueueTicketPriority;
use App\Enums\QueueTicketStatus;
use Database\Factories\QueueTicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueTicket extends Model
{
    /** @use HasFactory<QueueTicketFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'schedule_id',
        'queue_date',
        'queue_number',
        'prefix',
        'display_number',
        'status',
        'priority',
        'called_at',
        'served_at',
        'completed_at',
        'call_count',
        'counter',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'queue_date' => 'date',
            'queue_number' => 'integer',
            'called_at' => 'datetime',
            'served_at' => 'datetime',
            'completed_at' => 'datetime',
            'call_count' => 'integer',
            'status' => QueueTicketStatus::class,
            'priority' => QueueTicketPriority::class,
        ];
    }

    /**
     * Get the appointment associated with this queue ticket.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the doctor serving this queue ticket.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the schedule slot for this queue ticket.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
