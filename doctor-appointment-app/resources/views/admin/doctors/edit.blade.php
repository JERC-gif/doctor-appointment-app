{{-- Formulario para editar el perfil médico de un doctor existente --}}
<x-admin-layout title="Doctores | MediCitas" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
    ['name' => 'Editar'],
]">
    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Card superior: identidad del doctor y botones de acción --}}
        <x-wire-card class="mt-10">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    {{-- Avatar generado con las iniciales del nombre --}}
                    <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-600 font-bold text-xl">
                            {{ collect(explode(' ', $doctor->user->name))
                                ->map(fn($w) => strtoupper($w[0]))
                                ->implode('') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $doctor->user->name }}
                        </p>
                        {{-- Muestra N/A si no tiene licencia registrada --}}
                        <p class="text-sm text-gray-500">
                            Licencia: {{ $doctor->medical_license_number ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <x-wire-button outline href="{{ route('admin.doctors.index') }}">
                        Volver
                    </x-wire-button>
                    <x-wire-button type="submit">
                        <i class="fa-solid fa-check mr-2"></i> Guardar cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        {{-- Card inferior: campos editables del perfil médico --}}
        <x-wire-card class="mt-6">
            <div class="space-y-6">
                <x-wire-native-select label="Especialidad" name="speciality_id">
                    <option value="">Seleccione una especialidad</option>
                    @foreach ($specialities as $speciality)
                        <option value="{{ $speciality->id }}"
                            @selected(old('speciality_id', $doctor->speciality_id) == $speciality->id)>
                            {{ $speciality->name }}
                        </option>
                    @endforeach
                </x-wire-native-select>

                <x-wire-input
                    label="Número de licencia médica"
                    name="medical_license_number"
                    value="{{ old('medical_license_number', $doctor->medical_license_number) }}" />

                <x-wire-textarea
                    label="Biografía"
                    name="biography"
                    value="{{ old('biography', $doctor->biography) }}" />
            </div>
        </x-wire-card>
    </form>
</x-admin-layout>
