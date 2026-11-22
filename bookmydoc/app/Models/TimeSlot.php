<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TimeSlot extends Model
{
    protected $fillable = ['doctor_id', 'slot_start', 'slot_end', 'is_booked'];

    protected $casts = [
        'slot_start' => 'datetime',
        'slot_end' => 'datetime',
        'is_booked' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): HasOne
    {
        return $this->hasOne(Appointment::class);
    }
}
