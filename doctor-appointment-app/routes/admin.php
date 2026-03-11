<?php

// Rutas del panel de administración — prefijo 'admin.' aplicado en bootstrap/app.php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\ConsultationController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TicketController;
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

// Gestión de Doctores — incluye gestor de horarios (schedule)
Route::resource('doctors', DoctorController::class);
Route::get('doctors/{doctor}/schedule', [DoctorController::class, 'schedule'])->name('doctors.schedule');
Route::post('doctors/{doctor}/schedule', [DoctorController::class, 'saveSchedule'])->name('doctors.schedule.update');

// Gestión de Citas médicas — búsqueda de disponibilidad con resolución de conflictos
Route::resource('appointments', AppointmentController::class);

// Datos de la consulta y receta por cita
Route::get('appointments/{appointment}/consultation', [ConsultationController::class, 'show'])->name('consultations.show');
Route::post('appointments/{appointment}/consultation', [ConsultationController::class, 'store'])->name('consultations.store');

// Calendario por doctor — vista mensual, horarios disponibles/ocupados/no disponibles (index = view)
Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');

// Gestión de Tickets de Soporte — listado, crear, ver, editar, actualizar y eliminar
Route::resource('tickets', TicketController::class);
