<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AppointmentTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Appointment::query()->with(['patient.user', 'doctor.user']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),
            Column::make('Paciente', 'patient.user.name')->sortable()->searchable(),
            Column::make('Doctor', 'doctor.user.name')->sortable()->searchable(),
            Column::make('Fecha', 'appointment_date')->sortable()->format(fn ($v) => $v->format('d/m/Y')),
            Column::make('Hora', 'start_time')->sortable()->format(fn ($v) => is_string($v) ? substr($v, 0, 5) : $v->format('H:i')),
            Column::make('Hora fin', 'end_time')->format(fn ($v) => is_string($v) ? substr($v, 0, 5) : $v->format('H:i')),
            Column::make('Estado', 'status')
                ->sortable()
                ->format(fn ($value, $row) => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' .
                    match ($row->status) {
                        'programado' => 'bg-blue-100 text-blue-800',
                        'completado' => 'bg-green-100 text-green-800',
                        'cancelado' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800',
                    } . '">' . $row->status_label . '</span>'
                )
                ->html(),
            Column::make('Acciones')
                ->label(fn ($row) => view('admin.appointments.actions', ['appointment' => $row])),
        ];
    }
}
