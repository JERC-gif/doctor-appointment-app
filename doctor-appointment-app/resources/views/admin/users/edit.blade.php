<x-admin-layout title="Usuarios | Meditime"
                :breadcrumbs="[
    [
      'name' => 'Dashboard',
      'href' => route('admin.dashboard')
    ],
    [
      'name' => 'Usuarios',
      'href' => route('admin.users.index')
    ],
    [
      'name' => 'Editar'
    ],
]">
<x-wire-card>
        <form action="{{route('admin.users.update', $user->id)}}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-wire-input
                    label="Nombre"
                    name="name"
                    placeholder="Nombre del usuario"
                    value="{{old('name', $user->name)}}">
                </x-wire-input>

                <x-wire-input
                    label="Email"
                    name="email"
                    type="email"
                    placeholder="correo@ejemplo.com"
                    value="{{old('email', $user->email)}}">
                </x-wire-input>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-wire-input
                    label="Contraseña"
                    name="password"
                    type="password"
                    placeholder="Mínimo 8 caracteres">
                </x-wire-input>

                <x-wire-input
                    label="Confirmar contraseña"
                    name="password_confirmation"
                    type="password"
                    placeholder="Repita la contraseña">
                </x-wire-input>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-wire-input
                    label="Número de ID"
                    name="id_number"
                    placeholder="Número de identificación"
                    value="{{old('id_number', $user->id_number)}}">
                </x-wire-input>

                <x-wire-input
                    label="Teléfono"
                    name="phone"
                    placeholder="Número de teléfono"
                    value="{{old('phone', $user->phone)}}">
                </x-wire-input>
            </div>

            <x-wire-input
                label="Dirección"
                name="address"
                placeholder="Dirección completa"
                value="{{old('address', $user->address)}}">
            </x-wire-input>

            <div class="mb-4">
                <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Rol
                </label>
                <select
                    id="role_id"
                    name="role_id"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900
                           focus:border-indigo-500 focus:ring-indigo-500 @error('role_id') border-red-500 @enderror"
                >
                    <option value="">Sin rol</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define los permisos y accesos del usuario</p>
            </div>

            <div class="flex justify-end mt-4">
                <x-wire-button type="submit" blue>Actualizar</x-wire-button>
            </div>
        </form>
    </x-wire-card>

</x-admin-layout>
