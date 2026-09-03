<?php

namespace App\Models;

use App\Enums\AppointmentSource;
use App\Enums\AppointmentStatus;
use App\Enums\CheckInMethod;
use App\Enums\VisitType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'schedule_id',
        'booking_code',
        'appointment_date',
        'estimated_time',
        'visit_type',
        'chief_complaint',
        'patient_notes',
        'status',
        'source',
        'cancellation_reason',
        'cancelled_at',
        'checked_in_at',
        'check_in_method',
        'checked_in_by',
        'created_by',
        'metadata',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'cancelled_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'status' => AppointmentStatus::class,
            'visit_type' => VisitType::class,
            'source' => AppointmentSource::class,
            'check_in_method' => CheckInMethod::class,
            'metadata' => 'array',
        ];
    }

    /**
     * Get the patient who booked the appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor for this appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the schedule slot for this appointment.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the queue ticket generated for this appointment after check-in.
     */
    public function queueTicket(): HasOne
    {
        return $this->hasOne(QueueTicket::class);
    }

    /**
     * Get the user (staff/counter) who processed the check-in.
     */
    public function checkedInByStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }
}
