<x-admin-layout
    title="Roles | Meditime"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Roles',
            'href' => route('admin.roles.index'),
        ],
        [
            'name' => 'Nuevo',
        ]
    ]"
>

<x-wire-card>
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <x-wire-input
            label="Nombre"
            name="name"
            placeholder="Nombre del rol"
            value="{{ old('name') }}"
        ></x-wire-input>

        <div class="flex justify-start mt-4">
            <x-wire-button type="submit" blue>
                <i class="fa-solid fa-floppy-disk"></i> Guardar Rol
            </x-wire-button>
        </div>
    </form>
</x-wire-card>

</x-admin-layout>
