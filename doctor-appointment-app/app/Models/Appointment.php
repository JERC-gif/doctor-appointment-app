<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'reason',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    public const STATUS_PROGRAMADO = 'programado';
    public const STATUS_COMPLETADO = 'completado';
    public const STATUS_CANCELADO = 'cancelado';

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PROGRAMADO => 'Programado',
            self::STATUS_COMPLETADO => 'Completado',
            self::STATUS_CANCELADO  => 'Cancelado',
            default => $this->status,
        };
    }
}
