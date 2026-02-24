<?php

// Tabla Livewire para listar doctores con sus relaciones (usuario y especialidad)

namespace App\Livewire\Admin\Datatables;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DoctorTable extends DataTableComponent
{
    // Carga eager de relaciones para evitar N+1 queries
    public function builder(): Builder
    {
        return Doctor::query()->with('user', 'speciality');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")->sortable(),
            Column::make("Nombre", "user.name")->sortable(),
            Column::make("Email", "user.email")->sortable(),
            // Muestra N/A si el doctor no tiene especialidad asignada
            Column::make("Especialidad", "speciality.name")->sortable()
                ->format(fn($value) => $value ?? 'N/A'),
            // Muestra N/A si no tiene número de licencia registrado
            Column::make("Licencia", "medical_license_number")->sortable()
                ->format(fn($value) => $value ?? 'N/A'),
            Column::make("Acciones")
                ->label(fn($row) => view('admin.doctors.actions', ['doctor' => $row])),
        ];
    }
}
