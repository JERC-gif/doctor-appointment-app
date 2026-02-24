<?php

// Modelo para las especialidades médicas

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    protected $fillable = ['name'];

    // Una especialidad puede tener muchos doctores
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}