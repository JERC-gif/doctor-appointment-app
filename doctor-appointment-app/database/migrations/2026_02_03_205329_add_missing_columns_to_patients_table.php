<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Agregar columnas faltantes críticas
            if (!Schema::hasColumn('patients', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('blood_type_id');
            }
            if (!Schema::hasColumn('patients', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('patients', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('observations');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Eliminar columnas agregadas
            $table->dropColumn(['date_of_birth', 'gender', 'emergency_contact_name']);
        });
    }
};
