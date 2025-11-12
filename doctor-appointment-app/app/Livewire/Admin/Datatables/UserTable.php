<?php

namespace App\Livewire\Admin\DataTables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserTable extends DataTableComponent
{
    protected $model = User::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'asc')
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(10)
            ->setSearchEnabled()
            ->setColumnSelectEnabled()
            ->setPaginationEnabled()
            ->setPaginationMethod('simple')
            ->setEmptyMessage('No hay usuarios registrados.');
    }

    public function builder(): Builder
    {
        return User::query()->with('role');
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable()
                ->searchable(),
            Column::make("NOMBRE", "name")
                ->sortable()
                ->searchable(),
            Column::make("CORREO", "email")
                ->sortable()
                ->searchable(),
            Column::make("ROL")
                ->label(function($row) {
                    return $row->role ? $row->role->name : 'Sin rol';
                }),
            Column::make("ACCIONES")
                ->label(function($row){
                    return view('admin.users.actions',
                    ['user' => $row]);
                })
                ->unclickable()
        ];
    }
}
