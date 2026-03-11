<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

/**
 * Tabla Livewire para listar tickets de soporte con usuario, título, estado y fecha.
 */
class TicketTable extends DataTableComponent
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
            Column::make('ID', 'id')
                ->sortable()
                ->format(fn ($value) => '#' . $value),
            Column::make('Usuario', 'user.name')
                ->sortable()
                ->searchable(),
            Column::make('Título', 'title')
                ->sortable()
                ->searchable(),
            Column::make('Estado', 'status')
                ->sortable()
                ->format(fn ($value, $row) => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' .
                    match ($row->status) {
                        'abierto' => 'bg-yellow-100 text-yellow-800',
                        'en_proceso' => 'bg-blue-100 text-blue-800',
                        'cerrado' => 'bg-gray-100 text-gray-800',
                        default => 'bg-gray-100 text-gray-600',
                    } . '">' . $row->status_label . '</span>'
                )
                ->html(),
            Column::make('Fecha', 'created_at')
                ->sortable()
                ->format(fn ($value) => $value->format('d/m/Y H:i')),
            Column::make('Acciones')
                ->label(fn ($row) => view('admin.tickets.actions', ['ticket' => $row])),
        ];
    }
}
