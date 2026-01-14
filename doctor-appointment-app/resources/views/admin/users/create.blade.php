<x-admin-layout title="Usuarios | Meditime"
:breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Usuarios',
        'href' => route('admin.users.index'),
    ],
    [
        'name' => 'Nuevo',
    ]
]">
<x-wire-card>
    <form action="{{ route('admin.users.store')}}" method="POST">

        @csrf

        <x-wire-input
            label="Nombre"
            name="name"
            placeholder="Nombre del usuario"
            value="{{ old('name') }}"
        ></x-wire-input>

        <x-wire-input
            label="Correo"
            name="email"
            type="email"
            placeholder="correo@ejemplo.com"
            value="{{ old('email') }}"
        ></x-wire-input>

        <x-wire-input
            label="Contraseña"
            name="password"
            type="password"
            placeholder="Mínimo 8 caracteres"
        ></x-wire-input>

        <x-wire-input
            label="Confirmar contraseña"
            name="password_confirmation"
            type="password"
            placeholder="Confirma la contraseña"
        ></x-wire-input>

        <div class="mb-4">
            <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                Rol (opcional)
            </label>
            <select
                id="role_id"
                name="role_id"
                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900
                       focus:border-indigo-500 focus:ring-indigo-500 @error('role_id') border-red-500 @enderror"
            >
                <option value="">Seleccionar rol</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-start mt-4">
            <x-wire-button type="submit" blue>
                <i class="fa-solid fa-floppy-disk"></i> Guardar Usuario
            </x-wire-button>
        </div>
    </form>
</x-wire-card>
</x-admin-layout>

