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
        return User::query()
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as role_name');
    }

    public function columns(): array
    {
        return [
            Column::make("NAME", "name")
                ->sortable()
                ->searchable(),
            Column::make("EMAIL", "email")
                ->sortable()
                ->searchable(),
            Column::make("NÚMERO DE ID", "id_number")
                ->sortable()
                ->searchable(),
            Column::make("TELÉFONO", "phone")
                ->sortable()
                ->searchable(),
            Column::make("ROL", "role_name")
                ->label(function($row) {
                    return $row->role_name ?? 'Sin rol';
                }),
            Column::make("ACCIONES")
                ->label(function($row) {
                    return view('admin.users.actions', ['user' => $row, 'userId' => $row->id])->render();
                })
                ->html()
                ->unclickable()
        ];
    }
}
