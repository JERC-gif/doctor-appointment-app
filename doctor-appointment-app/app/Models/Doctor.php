<?php

// Modelo que representa el perfil médico de un usuario con rol Doctor

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'speciality_id',
        'medical_license_number',
        'biography',
    ];

    // Relación con el usuario dueño del perfil médico
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con la especialidad (puede ser null)
    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }

    /** Bloques de disponibilidad (15 min) por día de la semana */
    public function availability()
    {
        return $this->hasMany(DoctorAvailability::class, 'doctor_id');
    }

    /** Citas del doctor */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }
}