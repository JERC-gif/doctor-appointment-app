{{-- Nueva cita: buscar disponibilidad y elegir slot + paciente (UI moderna tipo Flowbite) --}}
<x-admin-layout
    title="Nueva Cita | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas', 'href' => route('admin.appointments.index')],
        ['name' => 'Nuevo'],
    ]"
>
    {{-- Bloque superior: Buscar disponibilidad (estilo barra) --}}
    <x-wire-card class="mt-10">
        <h2 class="text-xl font-bold text-gray-900">Buscar disponibilidad</h2>
        <p class="text-sm text-gray-500 mt-1 mb-4">Encuentra el horario perfecto para tu cita.</p>

        <form action="{{ route('admin.appointments.create') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                {{-- Fecha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                    <input type="date" name="date" value="{{ old('date', $date) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                </div>

                {{-- Hora (opcional, filtra los resultados) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora (opcional)</label>
                    @php
                        $ranges = [];
                        for ($h = 8; $h < 18; $h++) {
                            $from = sprintf('%02d:00', $h);
                            $to   = sprintf('%02d:00', $h + 1);
                            $ranges[] = $from . '-' . $to;
                        }
                    @endphp
                    <select name="time_range"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Cualquier hora</option>
                        @foreach ($ranges as $range)
                            <option value="{{ $range }}" @selected($timeRange === $range)>
                                {{ $range }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Especialidad --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad (opcional)</label>
                    <select name="speciality_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Selecciona una especialidad</option>
                        @foreach ($specialities as $s)
                            <option value="{{ $s->id }}" @selected(old('speciality_id', $specialityId) == $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex md:justify-end">
                    <x-wire-button type="submit" blue class="w-full md:w-auto">
                        <i class="fa-solid fa-search mr-2"></i>
                        Buscar disponibilidad
                    </x-wire-button>
                </div>
            </div>
        </form>
    </x-wire-card>

    {{-- Resultado de búsqueda: listado de doctores + resumen lateral --}}
    @if ($date)
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Columna izquierda: doctores y horarios --}}
            <div class="lg:col-span-2 space-y-4">
                @if ($slots->isEmpty())
                    <x-wire-card>
                        <div class="text-center py-10">
                            <i class="fa-solid fa-calendar-xmark text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-600 font-medium">No hay disponibilidad para la fecha seleccionada.</p>
                            <p class="text-sm text-gray-400 mt-1">Prueba otra fecha o especialidad.</p>
                        </div>
                    </x-wire-card>
                @else
                    @foreach ($slots->groupBy('doctor_id') as $doctorId => $doctorSlots)
                        @php
                            $doctor = $doctorSlots->first()['doctor'];
                        @endphp
                        <x-wire-card>
                            <div class="flex items-start gap-4">
                                {{-- Avatar simple con iniciales --}}
                                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-sm">
                                    {{ \Illuminate\Support\Str::of($doctor->user->name)->explode(' ')->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $doctor->user->name }}</h3>
                                    <p class="text-xs text-indigo-600 mt-0.5">{{ $doctor->speciality->name ?? 'Sin especialidad' }}</p>

                                    <p class="text-xs text-gray-500 mt-3 mb-1">Horarios disponibles:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($doctorSlots as $slot)
                                            @php
                                                $start = is_string($slot['start_time']) ? substr($slot['start_time'], 0, 5) : $slot['start_time']->format('H:i');
                                                $end   = is_string($slot['end_time']) ? substr($slot['end_time'], 0, 5) : $slot['end_time']->format('H:i');
                                            @endphp
                                            <button type="button"
                                                class="slot-button inline-flex items-center px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium hover:bg-indigo-100 border border-indigo-100"
                                                onclick="
                                                    document.getElementById('summary-doctor-id').value = '{{ $slot['doctor_id'] }}';
                                                    document.getElementById('summary-start-time').value = '{{ $start }}';
                                                    document.getElementById('summary-end-time').value = '{{ $end }}';
                                                    document.getElementById('summary-time-label').textContent = '{{ $start }} - {{ $end }}';
                                                    document.querySelectorAll('.slot-button').forEach(function(b){
                                                        b.classList.remove('bg-indigo-600','text-white','border-indigo-600');
                                                        b.classList.add('bg-indigo-50','text-indigo-700','border-indigo-100');
                                                    });
                                                    this.classList.remove('bg-indigo-50','text-indigo-700','border-indigo-100');
                                                    this.classList.add('bg-indigo-600','text-white','border-indigo-600');
                                                ">
                                                {{ $start }} - {{ $end }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </x-wire-card>
                    @endforeach
                @endif
            </div>

            {{-- Columna derecha: Resumen de la cita --}}
            <div>
                <x-wire-card>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Resumen de la cita</h3>
                    @if ($date && $slots->isNotEmpty())
                        <form action="{{ route('admin.appointments.store') }}" method="POST" class="space-y-4" id="appointment-summary-form">
                            @csrf

                            <input type="hidden" name="appointment_date" value="{{ $date }}">
                            <input type="hidden" name="doctor_id" id="summary-doctor-id">
                            <input type="hidden" name="start_time" id="summary-start-time">
                            <input type="hidden" name="end_time" id="summary-end-time">

                            <div class="space-y-3 text-sm text-gray-700 mb-2">
                                <div>
                                    <span class="font-medium">Fecha:</span>
                                    <span class="ml-1">{{ \Carbon\Carbon::parse($date)->translatedFormat('d/m/Y') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Hora seleccionada:</span>
                                    <span class="ml-1" id="summary-time-label">—</span>
                                </div>
                                <div>
                                    <span class="font-medium">Especialidad:</span>
                                    <span class="ml-1">
                                        @if ($specialityId)
                                            {{ optional($specialities->firstWhere('id', $specialityId))->name }}
                                        @else
                                            Cualquiera
                                        @endif
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">Selecciona un paciente, el motivo y un horario en la lista de doctores. Luego confirma para crear la cita.</p>
                            </div>

                            {{-- Paciente --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Paciente</label>
                                <select name="patient_id" id="sidebar-patient" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                    <option value="">Selecciona un paciente</option>
                                    @foreach ($patients as $p)
                                        <option value="{{ $p->id }}" @selected(request('patient_id') == $p->id)>
                                            {{ $p->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Motivo --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Motivo de la cita</label>
                                <textarea name="reason" id="sidebar-reason" rows="3"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="Ej. Chequeo de medicamentos, dolor de garganta, etc.">{{ request('reason') }}</textarea>
                            </div>

                            <p class="text-[11px] text-gray-400">La cita se creará con el horario seleccionado, el paciente y el motivo indicados.</p>
                            <x-wire-button type="submit" blue class="w-full justify-center">
                                <i class="fa-solid fa-calendar-check mr-2"></i>
                                Confirmar cita
                            </x-wire-button>
                        </form>
                    @else
                        <p class="text-sm text-gray-500">Busca disponibilidad para ver aquí el resumen de la cita.</p>
                    @endif
                </x-wire-card>
            </div>
        </div>
    @endif
</x-admin-layout>

