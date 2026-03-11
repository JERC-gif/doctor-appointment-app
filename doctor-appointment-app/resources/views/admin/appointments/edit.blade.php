{{-- Editar cita: cambiar paciente, doctor, fecha/hora y estado (validando conflictos) --}}
<x-admin-layout
    title="Editar cita | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas', 'href' => route('admin.appointments.index')],
        ['name' => 'Editar'],
    ]"
>
    <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST">
        @csrf
        @method('PUT')

        <x-wire-card class="mt-10">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Editar cita #{{ $appointment->id }}</h2>
                <div class="flex gap-2">
                    <x-wire-button outline gray href="{{ route('admin.appointments.index') }}">Volver</x-wire-button>
                    <x-wire-button type="submit" blue>Guardar cambios</x-wire-button>
                </div>
            </div>

            <div class="space-y-6">
                <x-wire-native-select label="Paciente" name="patient_id">
                    @foreach ($patients as $p)
                        <option value="{{ $p->id }}" @selected(old('patient_id', $appointment->patient_id) == $p->id)>{{ $p->user->name }}</option>
                    @endforeach
                </x-wire-native-select>

                <x-wire-native-select label="Doctor" name="doctor_id">
                    @foreach (\App\Models\Doctor::with('user')->get() as $d)
                        <option value="{{ $d->id }}" @selected(old('doctor_id', $appointment->doctor_id) == $d->id)>{{ $d->user->name }}</option>
                    @endforeach
                </x-wire-native-select>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="date" name="appointment_date" value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora inicio</label>
                        @php $st = $appointment->start_time; $startStr = is_string($st) ? substr($st, 0, 5) : $st->format('H:i'); @endphp
                        <input type="time" name="start_time" value="{{ old('start_time', $startStr) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora fin</label>
                        @php $et = $appointment->end_time; $endStr = is_string($et) ? substr($et, 0, 5) : $et->format('H:i'); @endphp
                        <input type="time" name="end_time" value="{{ old('end_time', $endStr) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm" required>
                    </div>
                </div>

                <x-wire-native-select label="Estado" name="status">
                    <option value="programado" @selected(old('status', $appointment->status) === 'programado')>Programado</option>
                    <option value="completado" @selected(old('status', $appointment->status) === 'completado')>Completado</option>
                    <option value="cancelado" @selected(old('status', $appointment->status) === 'cancelado')>Cancelado</option>
                </x-wire-native-select>
            </div>
        </x-wire-card>
    </form>
</x-admin-layout>
