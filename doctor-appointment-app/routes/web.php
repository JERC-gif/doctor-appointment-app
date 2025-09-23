<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin'); // Redirige al admin por defecto

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
