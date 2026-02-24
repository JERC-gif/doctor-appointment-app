<?php

// Rutas del panel de administración — prefijo 'admin.' aplicado en RouteServiceProvider

use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Dashboard principal
Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Gestión de Roles
Route::resource('roles', RoleController::class);

// Gestión de Usuarios
Route::resource('users', UserController::class);

// Gestión de Pacientes — se crean desde el módulo de Usuarios (rol "Paciente")
Route::resource('patients', PatientController::class)->except(['create', 'store']);

// Gestión de Doctores — el prefijo 'admin.' ya lo aplica el grupo en bootstrap/app.php
Route::resource('doctors', DoctorController::class);
