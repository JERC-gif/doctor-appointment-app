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

    @livewire('admin.data-tables.patient-table')

</x-admin-layout>