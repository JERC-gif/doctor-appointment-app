<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bloque de disponibilidad de 15 minutos para un doctor en un día de la semana.
 * day_of_week: 1 = Lunes, 7 = Domingo.
 */
class DoctorAvailability extends Model
{
    protected $table = 'doctor_availability';

    protected $fillable = ['doctor_id', 'day_of_week', 'start_time'];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /** Días de la semana para vistas (1 = Lunes) */
    public static function dayNames(): array
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
    }
}
