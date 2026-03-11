<x-admin-layout
    title="Nuevo Ticket | simify"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Soporte', 'href' => route('admin.tickets.index')],
        ['name' => 'Nuevo Ticket'],
    ]">

    <x-wire-card>
        <form action="{{ route('admin.tickets.store') }}" method="POST">
            @csrf

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-1">
                    <i class="fa-solid fa-headset text-blue-500 mr-2"></i>
                    Reportar un problema
                </h3>
                <p class="text-sm text-gray-500 mb-4">
                    Describe tu problema o duda y nuestro equipo de soporte se pondrá en contacto contigo.
                </p>

                <div class="space-y-4">
                    {{-- Título del problema --}}
                    <x-wire-input
                        label="Título del problema"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="Ej. No puedo acceder al calendario"
                        required
                    />

                    {{-- Descripción detallada --}}
                    <x-wire-textarea
                        label="Descripción detallada"
                        name="description"
                        value="{{ old('description') }}"
                        placeholder="Describe con detalle el problema que estás experimentando..."
                        required
                    />

                    {{-- Prioridad --}}
                    <x-wire-native-select label="Prioridad" name="priority" required>
                        <option value="">Selecciona la prioridad</option>
                        <option value="baja" @selected(old('priority') == 'baja')>🟢 Baja</option>
                        <option value="media" @selected(old('priority', 'media') == 'media')>🟡 Media</option>
                        <option value="alta" @selected(old('priority') == 'alta')>🔴 Alta</option>
                    </x-wire-native-select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <x-wire-button outline href="{{ route('admin.tickets.index') }}">
                    Cancelar
                </x-wire-button>
                <x-wire-button type="submit" blue>
                    <i class="fa-solid fa-paper-plane mr-1"></i>
                    Enviar Ticket
                </x-wire-button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>

