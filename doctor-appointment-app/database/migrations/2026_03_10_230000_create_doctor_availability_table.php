<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Disponibilidad del doctor en bloques de 15 minutos por día de la semana.
     * day_of_week: 1 = Lunes, 7 = Domingo.
     */
    public function up(): void
    {
        Schema::create('doctor_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->unsignedTinyInteger('day_of_week'); // 1-7
            $table->time('start_time'); // inicio del bloque de 15 min (ej. 08:00:00)
            $table->timestamps();

            $table->unique(['doctor_id', 'day_of_week', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_availability');
    }
};
