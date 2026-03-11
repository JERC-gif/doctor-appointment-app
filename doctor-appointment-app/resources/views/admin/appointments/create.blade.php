{{-- Nueva cita: buscar disponibilidad y elegir slot + paciente --}}
<x-admin-layout
    title="Nueva cita | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas', 'href' => route('admin.appointments.index')],
        ['name' => 'Nuevo'],
    ]"
>
    {{-- Card: Buscar disponibilidad --}}
    <x-wire-card class="mt-10">
        <h2 class="text-xl font-bold text-gray-900">Buscar disponibilidad</h2>
        <p class="text-sm text-gray-500 mt-1 mb-6">Encuentra el horario perfecto para tu cita.</p>

        <form action="{{ route('admin.appointments.create') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="min-w-[180px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                <input type="date" name="date" value="{{ old('date', $date) }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
            </div>
            <div class="min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad (opcional)</label>
                <select name="speciality_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Selecciona una especialidad</option>
                    @foreach ($specialities as $s)
                        <option value="{{ $s->id }}" @selected(old('speciality_id', $specialityId) == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <x-wire-button type="submit" blue>
                Buscar disponibilidad
            </x-wire-button>
        </form>
    </x-wire-card>

    @if ($date && $slots->isNotEmpty())
        <x-wire-card class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Slots disponibles para el {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h3>
            <p class="text-sm text-gray-500 mb-4">Elige un horario y asigna el paciente. Solo se muestran huecos sin conflicto.</p>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hora inicio</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hora fin</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($slots as $slot)
                            @php
                                $start = is_string($slot['start_time']) ? substr($slot['start_time'], 0, 5) : $slot['start_time']->format('H:i');
                                $end   = is_string($slot['end_time']) ? substr($slot['end_time'], 0, 5) : $slot['end_time']->format('H:i');
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $slot['doctor']->user->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $start }}</td>
                                <td class="px-4 py-3 text-sm">{{ $end }}</td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.appointments.store') }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="doctor_id" value="{{ $slot['doctor_id'] }}">
                                        <input type="hidden" name="appointment_date" value="{{ $date }}">
                                        <input type="hidden" name="start_time" value="{{ $start }}">
                                        <input type="hidden" name="end_time" value="{{ $end }}">
                                        <input type="text" name="reason" placeholder="Motivo de la cita (opcional)" class="rounded-lg border-gray-300 text-sm py-1.5 w-48" value="{{ old('reason') }}">
                                        <select name="patient_id" required class="rounded-lg border-gray-300 text-sm py-1.5">
                                            <option value="">Seleccionar paciente</option>
                                            @foreach ($patients as $p)
                                                <option value="{{ $p->id }}">{{ $p->user->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-wire-button type="submit" blue xs>Reservar</x-wire-button>
                                    </form>
                                </td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-wire-card>
    @elseif ($date && $slots->isEmpty())
        <x-wire-card class="mt-6">
            <p class="text-gray-600">No hay disponibilidad para la fecha seleccionada. Prueba otra fecha o especialidad.</p>
        </x-wire-card>
    @endif
</x-admin-layout>
