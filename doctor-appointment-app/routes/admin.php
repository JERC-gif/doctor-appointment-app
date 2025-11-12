<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController; // 👈 Importa tu nuevo controlador

use Illuminate\Support\Facades\Route;

// Dashboard principal
Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Gestión de Roles
Route::resource('roles', RoleController::class);

// Gestión de Usuarios 👇
Route::resource('users', UserController::class);
