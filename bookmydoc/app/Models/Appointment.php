<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'clinic_id', 'time_slot_id', 'scheduled_at', 'status', 'chief_complaint',
    ];

    protected $casts = ['scheduled_at' => 'datetime'];

    /** Human-friendly booking reference, e.g. BMD-000123. */
    public function getReferenceAttribute(): string
    {
        return 'BMD-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
