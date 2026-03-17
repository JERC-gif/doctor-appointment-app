{{-- Gestor de horarios del doctor: grid DÍA/HORA con bloques de 15 min — guardado por POST --}}
<x-admin-layout
    title="Horarios | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
        ['name' => $doctor->user->name, 'href' => route('admin.doctors.edit', $doctor)],
        ['name' => 'Horarios'],
    ]"
>
    @php
        $days = \App\Models\DoctorAvailability::dayNames();
        $hourStart = 8;
        $hourEnd = 18;
        $blocks = [];
        for ($h = $hourStart; $h < $hourEnd; $h++) {
            for ($q = 0; $q < 4; $q++) {
                $m = $q * 15;
                $blocks[] = sprintf('%02d:%02d', $h, $m);
            }
        }
        $saved = [];
        foreach ($doctor->availability->groupBy('day_of_week') as $day => $items) {
            $saved[$day] = [];
            foreach ($items as $a) {
                $t = $a->start_time;
                $saved[$day][is_string($t) ? substr($t, 0, 5) : $t->format('H:i')] = true;
            }
        }
    @endphp

    <form action="{{ route('admin.doctors.schedule.update', $doctor) }}" method="POST" id="form-schedule">
        @csrf

        <x-wire-card class="mt-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Gestor de horarios</h2>
                    <p class="text-sm text-gray-500 mt-1">Doctor: <strong>{{ $doctor->user->name }}</strong></p>
                    <p class="text-xs text-gray-400 mt-1">Marca los bloques de 15 min en los que el doctor está disponible. Las citas se ofrecerán solo en estos horarios.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.calendar.index', ['doctor_id' => $doctor->id]) }}"
                        class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <i class="fa-solid fa-calendar-days mr-2"></i> Ver calendario
                    </a>
                    <x-wire-button type="submit" blue>
                        <i class="fa-solid fa-save mr-2"></i> Guardar horario
                    </x-wire-button>
                </div>
            </div>

            {{-- Leyenda rápida --}}
            <div class="flex flex-wrap gap-4 mb-4 p-3 bg-gray-50 rounded-lg text-sm">
                <span><span class="inline-block w-4 h-4 rounded border-2 border-gray-300 bg-white mr-1"></span> No disponible</span>
                <span><span class="inline-block w-4 h-4 rounded border-2 border-green-500 bg-green-100 mr-1"></span> Disponible (marcado)</span>
                <span class="text-gray-500">Usa «Todos» en una celda para marcar/desmarcar la hora entera (4 bloques de 15 min).</span>
            </div>

            <div class="overflow-x-auto -mx-2">
                <table class="min-w-full border border-gray-200 rounded-lg schedule-grid">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b sticky left-0 bg-gray-50 z-10 min-w-[120px]">Día / Hora</th>
                            @foreach ($days as $num => $name)
                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase border-b whitespace-nowrap min-w-[110px]">
                                    <div class="flex flex-col items-center gap-1">
                                        <span>{{ $name }}</span>
                                        @php
                                            $totalSlotsPerDay = count($blocks);
                                            $selectedSlotsPerDay = isset($saved[$num]) ? count($saved[$num]) : 0;
                                            $dayAllChecked = $totalSlotsPerDay > 0 && $selectedSlotsPerDay === $totalSlotsPerDay;
                                        @endphp
                                        <label class="flex items-center gap-1 text-[11px] text-gray-500 cursor-pointer">
                                            <input type="checkbox"
                                                   class="day-all rounded border-gray-400 text-indigo-600 focus:ring-indigo-500"
                                                   data-day="{{ $num }}"
                                                   @checked($dayAllChecked)
                                            >
                                            <span>Todo el día</span>
                                        </label>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (array_chunk($blocks, 4) as $hourBlock)
                            @php $hourLabel = $hourBlock[0]; @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                                <td class="px-3 py-2 text-sm font-medium text-gray-700 align-top sticky left-0 bg-white z-10">
                                    <span class="whitespace-nowrap">{{ $hourLabel }}</span>
                                </td>
                                @foreach ($days as $dayNum => $dayName)
                                    <td class="px-2 py-2 align-top cell-day" data-day="{{ $dayNum }}">
                                        <label class="flex items-center gap-1.5 mb-2 cursor-pointer text-xs text-gray-600 hover:text-gray-900">
                                            <input type="checkbox" class="hour-all rounded border-gray-400 text-indigo-600 focus:ring-indigo-500"
                                                @checked(count(array_intersect($hourBlock, array_keys($saved[$dayNum] ?? []))) === 4)
                                            >
                                            <span>Todos</span>
                                        </label>
                                        @foreach ($hourBlock as $slot)
                                            <label class="flex items-center gap-1.5 py-0.5 cursor-pointer schedule-slot {{ isset($saved[$dayNum][$slot]) ? 'slot-checked' : '' }}">
                                                <input type="checkbox" name="slots[{{ $dayNum }}][{{ $slot }}]" value="1"
                                                    class="slot-check rounded border-gray-400 text-indigo-600 focus:ring-indigo-500"
                                                    @checked(isset($saved[$dayNum][$slot]))
                                                >
                                                <span class="text-xs text-gray-600">{{ $slot }}</span>
                                            </label>
                                        @endforeach
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="mt-4 text-sm text-gray-500">Después de marcar los bloques, haz clic en <strong>Guardar horario</strong> para que los cambios se apliquen.</p>
        </x-wire-card>
    </form>

    <style>
        .schedule-grid .cell-day { min-width: 90px; }
        .schedule-slot.slot-checked { background: rgb(220 252 231); border-radius: 4px; }
    </style>

    <script>
        (function() {
            var form = document.getElementById('form-schedule');
            if (!form) return;

            // "Todo el día" en el encabezado: marcar/desmarcar todos los slots de ese día
            form.querySelectorAll('.day-all').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    var day = this.getAttribute('data-day');
                    var dayCells = form.querySelectorAll('td.cell-day[data-day=\"' + day + '\"]');
                    dayCells.forEach(function(cell) {
                        cell.querySelectorAll('.slot-check').forEach(function(s) {
                            s.checked = cb.checked;
                            s.closest('.schedule-slot').classList.toggle('slot-checked', s.checked);
                        });
                        // Actualizar el checkbox \"Todos\" de cada celda de hora
                        var hourAll = cell.querySelector('.hour-all');
                        if (hourAll) {
                            hourAll.checked = cb.checked;
                        }
                    });
                });
            });

            // "Todos" en una celda: marcar/desmarcar los 4 bloques de esa hora
            form.querySelectorAll('.hour-all').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    var cell = this.closest('td');
                    cell.querySelectorAll('.slot-check').forEach(function(s) {
                        s.checked = cb.checked;
                        s.closest('.schedule-slot').classList.toggle('slot-checked', s.checked);
                    });

                    // Actualizar el checkbox de \"Todo el día\" correspondiente
                    var day = cell.getAttribute('data-day');
                    if (day !== null) {
                        var daySlots = form.querySelectorAll('td.cell-day[data-day=\"' + day + '\"] .slot-check');
                        var dayAll = form.querySelector('.day-all[data-day=\"' + day + '\"]');
                        if (dayAll) {
                            var allChecked = Array.from(daySlots).every(function(e) { return e.checked; });
                            dayAll.checked = allChecked;
                        }
                    }
                });
            });

            // Al marcar/desmarcar un slot, actualizar estilo, el \"Todos\" de la celda y el \"Todo el día\"
            form.querySelectorAll('.slot-check').forEach(function(s) {
                s.addEventListener('change', function() {
                    this.closest('.schedule-slot').classList.toggle('slot-checked', this.checked);
                    var cell = this.closest('td');
                    var all = cell.querySelectorAll('.slot-check');
                    var hourAll = cell.querySelector('.hour-all');
                    if (hourAll) {
                        hourAll.checked = all.length === Array.from(all).filter(function(e) { return e.checked; }).length;
                    }

                    // Actualizar el checkbox \"Todo el día\" para ese día
                    var day = cell.getAttribute('data-day');
                    if (day !== null) {
                        var daySlots = form.querySelectorAll('td.cell-day[data-day=\"' + day + '\"] .slot-check');
                        var dayAll = form.querySelector('.day-all[data-day=\"' + day + '\"]');
                        if (dayAll) {
                            var allChecked = Array.from(daySlots).every(function(e) { return e.checked; });
                            dayAll.checked = allChecked;
                        }
                    }
                });
            });
        })();
    </script>
</x-admin-layout>
