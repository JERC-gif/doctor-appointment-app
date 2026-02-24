<?php

// Crea la tabla 'doctors' vinculada a un usuario y una especialidad

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Especialidad opcional; si se elimina la especialidad el campo queda en null
            $table->foreignId('speciality_id')->nullable()->constrained('specialities')->onDelete('set null');
            $table->string('medical_license_number')->nullable();
            $table->text('biography')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
