<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blood_type_id',
        'date_of_birth',
        'gender',
        'allergies',
        'chronic_diseases',
        'surgery_history',
        'family_history',
        'observations',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_relationship',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Alias para la vista: formulario usa "chronic_conditions", BD tiene "chronic_diseases".
     */
    public function getChronicConditionsAttribute(): ?string
    {
        return $this->attributes['chronic_diseases'] ?? null;
    }

    /**
     * Alias para la vista: formulario usa "surgical_history", BD tiene "surgery_history".
     */
    public function getSurgicalHistoryAttribute(): ?string
    {
        return $this->attributes['surgery_history'] ?? null;
    }

    /**
     * Obtener el usuario asociado al paciente.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el tipo de sangre del paciente.
     */
    public function bloodType(): BelongsTo
    {
        return $this->belongsTo(BloodType::class);
    }
}
