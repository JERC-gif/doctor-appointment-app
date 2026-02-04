<x-admin-layout
    title="Pacientes | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Pacientes',
        ],
    ]"
>

    <x-slot name="action">
        <x-wire-button blue href="{{ route('admin.patients.create') }}">
            <i class="fa-solid fa-plus"></i>
            Nuevo
        </x-wire-button>
    </x-slot>

    @livewire('admin.data-tables.patient-table')

</x-admin-layout>