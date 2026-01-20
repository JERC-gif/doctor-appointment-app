<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Usamos la cualidad para refrecar DB entre test
uses(RefreshDatabase::class);

test('Un Usuario no puede eliminar a si mismo', function () {
    //Crear un Usuario de prueba
    $user = User::factory()->create();

    //Simulamos que ya inicio sesion
    $this->actingAs($user, 'web');

    //Simular una peticion HTTP DELETE
    $response =$this->delete(route('admin.users.destroy', $user->id));

    //Esperamos que el servidor bloquee la accion (403 Forbidden)
    $response->assertStatus(403);

    //Verificar que el usuario siga existiendo
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);
});
