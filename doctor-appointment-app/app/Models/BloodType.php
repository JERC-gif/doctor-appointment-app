<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BloodType extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Obtener los pacientes con este tipo de sangre.
     */
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}