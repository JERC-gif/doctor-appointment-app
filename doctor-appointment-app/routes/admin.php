<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;

use Illuminate\Support\Facades\Route;

// Dashboard principal
Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Gestión de Roles
Route::resource('roles', RoleController::class);

// Gestión de Usuarios 👇
Route::resource('users', UserController::class);

// Gestión de Pacientes 👇
// Solo permite: index, show, edit, update, destroy
// Los pacientes se crean desde el módulo de Usuarios con rol "Paciente"
Route::resource('patients', PatientController::class)->except(['create', 'store']);
