<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('No se puede actualizar un usuario con nombre vacio', function () {
    // Usuario autenticado para pasar middleware
    $actor = User::factory()->create();
    $this->actingAs($actor, 'web');

    // Usuario objetivo
    $target = User::factory()->create([
        'name' => 'NombreOriginal',
    ]);

    // Request JSON para que Laravel responda 422 (no 302)
    $response = $this->withHeaders(['Accept' => 'application/json'])
        ->put(route('admin.users.update', $target->id), [
            'name' => '', // inválido
            'email' => $target->email, // mantener válido
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);

    // Verificar que NO se guardó el nombre vacío
    $target->refresh();
    expect($target->name)->toBe('NombreOriginal');
});
