{{-- Módulo de calendarios: vista como en referencia (citas en celdas, controles Hoy, Mes/Semana/Día/Lista) --}}
<x-admin-layout
    title="Calendario | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Calendario'],
    ]"
>
    <div class="mt-10 space-y-6">
        {{-- Título tipo referencia: A futuro / Módulo de calendarios --}}
        <div class="mb-2">
            <h1 class="text-lg font-semibold text-gray-800">A futuro</h1>
            <p class="text-2xl font-bold text-gray-900">Módulo de calendarios</p>
        </div>

        {{-- Selector de doctor y Gestionar horario --}}
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Doctor:</label>
                <form method="GET" action="{{ route('admin.calendar.index') }}" class="inline" id="form-doctor">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    @if($selectedDate)<input type="hidden" name="date" value="{{ $selectedDate }}">@endif
                    <select name="doctor_id" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-w-[220px]">
                        @foreach($doctors as $d)
                            <option value="{{ $d->id }}" @selected($currentDoctor && $currentDoctor->id == $d->id)>
                                {{ $d->user->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            @if($currentDoctor)
                <a href="{{ route('admin.doctors.schedule', $currentDoctor) }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                    <i class="fa-solid fa-clock mr-2"></i> Gestionar horario
                </a>
            @endif
        </div>

        @if(!$doctors->isEmpty())
            @php
                $prev = Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
                $next = Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
                $baseParams = ['doctor_id' => $currentDoctor?->id, 'year' => $year, 'month' => $month];
                if ($selectedDate) $baseParams['date'] = $selectedDate;
                $todayParams = array_merge($baseParams, ['year' => now()->year, 'month' => now()->month, 'date' => now()->format('Y-m-d')]);
            @endphp

            {{-- Controles: < > Hoy — mes de año — Mes | Semana | Día | Lista --}}
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.calendar.index', array_merge($baseParams, ['year' => $prev->year, 'month' => $prev->month])) }}"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-700 text-white hover:bg-gray-600">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    <a href="{{ route('admin.calendar.index', array_merge($baseParams, ['year' => $next->year, 'month' => $next->month])) }}"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-700 text-white hover:bg-gray-600">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    <a href="{{ route('admin.calendar.index', $todayParams) }}"
                        class="px-4 py-2 rounded-lg bg-gray-700 text-white text-sm font-medium hover:bg-gray-600">
                        Hoy
                    </a>
                </div>
                <h2 class="text-xl font-bold text-gray-900 capitalize">{{ $monthName }} de {{ $year }}</h2>
                <div class="flex rounded-lg overflow-hidden border border-gray-300">
                    <a href="{{ route('admin.calendar.index', $baseParams) }}"
                        class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white">
                        Mes
                    </a>
                    <span class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 border-l border-gray-300">Semana</span>
                    <span class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 border-l border-gray-300">Día</span>
                    <span class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 border-l border-gray-300">Lista</span>
                </div>
            </div>

            {{-- Grid mensual: dom → sáb, citas en cada celda --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="py-2 text-xs font-medium text-gray-500 uppercase w-[14%]">dom</th>
                            <th class="py-2 text-xs font-medium text-gray-500 uppercase w-[14%]">lun</th>
                            <th class="py-2 text-xs font-medium text-gray-500 uppercase w-[14%]">mar</th>
                            <th class="py-2 text-xs font-medium text-gray-500 uppercase w-[14%]">mié</th>
                            <th class="py-2 text-xs font-medium text-gray-500 uppercase w-[14%]">jue</th>
                            <th class="py-2 text-xs font-medium text-gray-500 uppercase w-[14%]">vie</th>
                            <th class="py-2 text-xs font-medium text-gray-500 uppercase w-[14%]">sáb</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weeks as $week)
                            <tr class="border-b border-gray-100">
                                @foreach($week as $cell)
                                    @php
                                        $d = $cell['date'];
                                        $dateStr = $d->format('Y-m-d');
                                        $url = route('admin.calendar.index', [
                                            'doctor_id' => $currentDoctor?->id,
                                            'year' => $year,
                                            'month' => $month,
                                            'date' => $dateStr,
                                        ]);
                                        $dayAppointments = $appointmentsByDate[$dateStr] ?? [];
                                    @endphp
                                    <td class="align-top p-1 border-r border-gray-100 last:border-r-0 min-h-[100px]">
                                        <a href="{{ $url }}"
                                            class="block rounded-lg p-2 min-h-[90px] {{ $cell['isCurrentMonth'] ? 'text-gray-900 hover:bg-indigo-50' : 'text-gray-400 bg-gray-50/50' }} {{ $selectedDate === $dateStr ? 'ring-2 ring-indigo-500 bg-indigo-50' : '' }} {{ $cell['isToday'] ? 'bg-amber-100' : '' }}">
                                            <span class="text-sm font-medium {{ $cell['isToday'] && $cell['isCurrentMonth'] ? 'bg-amber-500 text-white rounded-full w-7 h-7 inline-flex items-center justify-center' : '' }}">
                                                {{ $d->day }}
                                            </span>
                                            <div class="mt-1 space-y-0.5">
                                                @foreach($dayAppointments as $apt)
                                                    @php
                                                        $dotColor = match($apt->status) {
                                                            \App\Models\Appointment::STATUS_COMPLETADO => 'bg-green-500',
                                                            \App\Models\Appointment::STATUS_CANCELADO => 'bg-red-500',
                                                            default => 'bg-blue-500',
                                                        };
                                                        $patientName = $apt->patient->user->name ?? 'Paciente';
                                                    @endphp
                                                    <div class="flex items-center gap-1 text-xs truncate">
                                                        <span class="text-gray-600 shrink-0">{{ \Carbon\Carbon::parse($apt->start_time)->format('H:i') }}</span>
                                                        <span class="inline-block w-2 h-2 rounded-full shrink-0 {{ $dotColor }}"></span>
                                                        <span class="truncate">{{ $patientName }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </a>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Leyenda --}}
            <div class="flex flex-wrap gap-6 text-sm text-gray-600">
                <span><span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-1"></span> Programado</span>
                <span><span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-1"></span> Completado</span>
                <span><span class="inline-block w-2 h-2 rounded-full bg-red-500 mr-1"></span> Cancelado</span>
            </div>

            {{-- Detalle del día seleccionado --}}
            @if($selectedDate && $currentDoctor)
                <x-wire-card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">
                            Horario del día — {{ \Carbon\Carbon::parse($selectedDate)->locale('es')->translatedFormat('l d \d\e F \d\e Y') }}
                        </h3>
                        <a href="{{ route('admin.appointments.create') }}?date={{ $selectedDate }}"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            + Nueva cita este día
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cita</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($daySlots as $slot)
                                    @php
                                        $bg = match($slot['status']) {
                                            'available' => 'bg-green-100 text-green-800',
                                            'occupied' => 'bg-gray-200 text-gray-800',
                                            default => 'bg-red-100 text-red-800',
                                        };
                                        $label = match($slot['status']) {
                                            'available' => 'Disponible',
                                            'occupied' => 'Ocupado',
                                            default => 'No disponible',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-2 text-sm">{{ $slot['start'] }} - {{ $slot['end'] }}</td>
                                        <td class="px-4 py-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $bg }}">{{ $label }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            @if($slot['appointment'])
                                                {{ $slot['appointment']->patient->user->name ?? '—' }}
                                                <a href="{{ route('admin.appointments.edit', $slot['appointment']) }}" class="text-indigo-600 hover:underline ml-2">Editar</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-wire-card>
            @endif
        @else
            <x-wire-card>
                <p class="text-gray-600">No hay doctores registrados. Crea al menos un doctor para usar el calendario.</p>
            </x-wire-card>
        @endif
    </div>
</x-admin-layout>
