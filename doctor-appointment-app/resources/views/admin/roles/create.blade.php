<x-admin-layout
    title="Roles | HouseMD"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Roles',     'href' => route('admin.roles.index')],
        ['name' => 'Nuevo']
    ]"
>
    <div class="max-w-3xl mx-auto py-6 px-4">
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Nuevo</h2>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 p-6">
            <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        Nombre
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        required
                        value="{{ old('name') }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900
                               focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <x-wire-button type="submit" blue>
                        Guardar
                    </x-wire-button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
