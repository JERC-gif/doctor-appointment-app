<?php

// Crea la tabla 'specialities' para las especialidades médicas disponibles

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nombre único de la especialidad
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialities');
    }
};
