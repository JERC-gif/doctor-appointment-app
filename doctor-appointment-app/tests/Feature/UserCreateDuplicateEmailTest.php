<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('No se puede crear un usuario con email duplicado', function () {
    // Usuario autenticado para pasar middleware (auth)
    $actor = User::factory()->create();
    $this->actingAs($actor, 'web');

    // Usuario existente con el mismo email
    $existing = User::factory()->create([
        'email' => 'duplicado@example.com',
    ]);

    // Forzamos respuesta JSON (302)
    $response = $this->withHeaders(['Accept' => 'application/json'])
        ->post(route('admin.users.store'), [
            'name' => 'Nuevo',
            'email' => $existing->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);

    $this->assertDatabaseMissing('users', [
        'name' => 'Nuevo',
        'email' => $existing->email,
    ]);
});
