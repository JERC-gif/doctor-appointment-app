<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de ticket de soporte.
 * Cada ticket pertenece a un usuario y tiene título, descripción y estado.
 */
class Ticket extends Model
{
    use HasFactory;

    /** Atributos asignables en masa */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'admin_response',
    ];

    /** Valores posibles del estado del ticket */
    public const STATUS_ABIERTO = 'abierto';
    public const STATUS_EN_PROCESO = 'en_proceso';
    public const STATUS_CERRADO = 'cerrado';

    /**
     * Obtener el usuario que creó el ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Etiqueta legible del estado para la vista.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ABIERTO => 'Abierto',
            self::STATUS_EN_PROCESO => 'En proceso',
            self::STATUS_CERRADO => 'Cerrado',
            default => $this->status,
        };
    }
}
