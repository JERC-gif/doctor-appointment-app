{{-- Listado de doctores — los doctores se crean desde Usuarios asignando el rol 'doctor' --}}
<x-admin-layout title="Doctores | MediCitas" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Doctores'],
]">
    @livewire('admin.datatables.doctor-table')
</x-admin-layout>
