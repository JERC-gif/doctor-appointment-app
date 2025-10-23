<x-admin-layout title="Roles | MediLink" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'url' => route('admin.dashboard')
    ],
    [
        'name' => 'Roles'
    ],
]">
    @livewire('admin.datatables.role-table')
</x-admin-layout>
