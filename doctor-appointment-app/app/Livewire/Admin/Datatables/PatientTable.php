<?php

namespace App\Livewire\Admin\DataTables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;

class PatientTable extends DataTableComponent
{

    public function builder(): Builder
    {
        return Patient::query()->with(['user', 'bloodType']);
    }

    protected $model = Patient::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()
                ->searchable(),
            Column::make("Nombre", "user.name")
                ->sortable()
                ->searchable()
                ->format(fn($value, $row) =>
                    ($row->allergies
                        ? '<span class="inline-flex items-center"><i class="fa-solid fa-triangle-exclamation text-red-500 mr-2" title="Paciente con alergias registradas"></i>' . $value . '</span>'
                        : $value
                    )
                )
                ->html(),
            Column::make("Email", "user.email")
                ->sortable()
                ->searchable(),
            Column::make("Teléfono", "user.phone")
                ->sortable()
                ->searchable(),
            Column::make("Fecha Nac.", "date_of_birth")
                ->sortable()
                ->format(fn($value) => $value ? $value->format('d/m/Y') : 'No especificada'),
            Column::make("Género", "gender")
                ->sortable()
                ->format(fn($value) => match($value) {
                    'male' => 'Masculino',
                    'female' => 'Femenino',
                    default => 'Otro',
                }),
            Column::make("Tipo Sangre", "bloodType.name")
                ->sortable()
                ->format(fn($value, $row) => $value
                    ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">' . $value . '</span>'
                    : '<span class="text-gray-400">No especificado</span>'
                )
                ->html(),
            Column::make("Acciones")
                ->label(function($row){
                    return view('admin.patients.actions',
                        ['patient' => $row]);
                }),
        ];
    }
}
