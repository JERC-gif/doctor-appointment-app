<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;


uses(RefreshDatabase::class);

test('No se puede actualizar la contraseña con un password debil', function () {
    // Usuario que hace la acción (para pasar middleware auth)
    $actor = User::factory()->create();
    $this->actingAs($actor, 'web');

    // Usuario objetivo a actualizar
    $target = User::factory()->create([
        'password' => Hash::make('OldPassword123'),
    ]);

    // Mandamos Accept JSON para que Laravel responda 422 en vez de 302
    $response = $this->withHeaders(['Accept' => 'application/json'])
        ->put(route('admin.users.update', $target->id), [
            'name' => $target->name,
            'email' => $target->email,

            // Password inválido (muy corto)
            'password' => '123',
            'password_confirmation' => '123',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);

    // Confirmar que el password NO cambió
    $target->refresh();
    expect(Hash::check('OldPassword123', $target->password))->toBeTrue();
});