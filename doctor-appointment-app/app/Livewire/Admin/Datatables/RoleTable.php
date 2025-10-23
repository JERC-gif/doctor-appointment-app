<?php

namespace App\Livewire\Admin\Datatables;

use Livewire\Component;
use App\Models\Role;

class RoleTable extends Component
{
    public $roles;
    public $search = '';

    public function mount()
    {
        $this->loadRoles();
    }

    public function loadRoles()
    {
        $query = Role::query();
        
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('guard_name', 'like', '%' . $this->search . '%');
        }
        
        $this->roles = $query->orderBy('id', 'desc')->get();
    }

    public function updatedSearch()
    {
        $this->loadRoles();
    }

    public function render()
    {
        return view('livewire.admin.datatables.role-table');
    }
}
