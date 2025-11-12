<x-admin-layout title="Usuarios | Meditime"
:breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Usuarios',
    ]
]">

    <x-slot name="action">
        <x-wire-button blue href="{{ route('admin.users.create') }}">
            <i class="fa-solid fa-user-plus"></i>
            Nuevo Usuario
        </x-wire-button>
    </x-slot>

    {{-- Contenido en blanco por ahora --}}

</x-admin-layout>

