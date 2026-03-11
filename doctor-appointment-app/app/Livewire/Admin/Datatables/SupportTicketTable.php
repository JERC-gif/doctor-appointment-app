<?php

namespace App\Livewire\Admin\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Ticket;

class SupportTicketTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Ticket::query()->with('user');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()
                ->format(fn ($value) => '#' . $value),

            Column::make("Usuario", "user.name")
                ->sortable()
                ->searchable(),

            Column::make("Título", "title")
                ->sortable()
                ->searchable(),

            Column::make("Estado", "status")
                ->sortable()
                ->format(function ($value) {
                    $colors = [
                        'abierto'     => 'bg-yellow-100 text-yellow-800',
                        'en_progreso' => 'bg-blue-100 text-blue-800',
                        'cerrado'     => 'bg-green-100 text-green-800',
                    ];
                    $labels = [
                        'abierto'     => 'Abierto',
                        'en_progreso' => 'En Progreso',
                        'cerrado'     => 'Cerrado',
                    ];
                    $class = $colors[$value] ?? 'bg-gray-100 text-gray-800';
                    $label = $labels[$value] ?? $value;
                    return '<span class="px-2 py-1 rounded-full text-xs font-semibold ' . $class . '">' . $label . '</span>';
                })
                ->html(),

            Column::make("Prioridad", "priority")
                ->sortable()
                ->format(function ($value) {
                    $colors = [
                        'baja'  => 'bg-gray-100 text-gray-800',
                        'media' => 'bg-orange-100 text-orange-800',
                        'alta'  => 'bg-red-100 text-red-800',
                    ];
                    $labels = [
                        'baja'  => 'Baja',
                        'media' => 'Media',
                        'alta'  => 'Alta',
                    ];
                    $class = $colors[$value] ?? 'bg-gray-100 text-gray-800';
                    $label = $labels[$value] ?? $value;
                    return '<span class="px-2 py-1 rounded-full text-xs font-semibold ' . $class . '">' . $label . '</span>';
                })
                ->html(),

            Column::make("Fecha", "created_at")
                ->sortable()
                ->format(fn ($value) => $value->format('d/m/Y H:i')),

            Column::make("Acciones")
                ->label(fn ($row) => view('admin.tickets.actions', ['ticket' => $row])),
        ];
    }
}

