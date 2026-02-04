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
        Schema::create('patients', function (Blueprint $table) {
            //campos añadidos
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->foreignId('blood_type_id')->nullable()->constrained('blood_types')->onDelete('set null');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->text('allergies')->nullable();
            $table->text('chronic_conditions')->nullable();
            $table->text('surgical_history')->nullable();
            $table->text('observations')->nullable();
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone', 20);
            $table->string('emergency_relationship')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};