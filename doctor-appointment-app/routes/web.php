<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['ok' => true]);
});

Route::get('/dashboard', function () {
    return response()->json(['message' => 'dashboard']);
})->name('dashboard');

// Las rutas del panel admin (dashboard, roles, usuarios, pacientes, doctores, tickets de soporte, etc.)
// están definidas en routes/admin.php y se cargan con prefijo 'admin' desde bootstrap/app.php

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard de administrador
    Route::get('/dashboard', function () {
        return view('admin.dashboard'); // vista en resources/views/admin/dashboard.blade.php
    })->name('dashboard');
});
