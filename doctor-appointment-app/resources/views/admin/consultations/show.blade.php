{{-- Datos de la consulta: tabs Consulta / Receta, modales Historia y Consultas Anteriores --}}
@php
    $patient = $appointment->patient;
@endphp
<x-admin-layout
    title="Consulta | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas', 'href' => route('admin.appointments.index')],
        ['name' => 'Consulta'],
    ]"
>
    <x-wire-card class="mt-10">
        {{-- Header: paciente + DNI + botones --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $patient->user->name }}</h2>
                <p class="text-sm text-gray-500">DNI: {{ $patient->user->id_number ?? '—' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" data-modal-target="modal-historia" data-modal-toggle="modal-historia"
                    class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-file-medical mr-2"></i> Ver Historia
                </button>
                <button type="button" data-modal-target="modal-consultas-anteriores" data-modal-toggle="modal-consultas-anteriores"
                    class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-history mr-2"></i> Consultas Anteriores
                </button>
            </div>
        </div>

        <form action="{{ route('admin.consultations.store', $appointment) }}" method="POST">
            @csrf

            {{-- Tabs Consulta / Receta --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex gap-6" aria-label="Tabs">
                    <button type="button" data-tab="consulta" class="tab-btn py-3 px-1 border-b-2 font-medium text-sm text-indigo-600 border-indigo-500">
                        Consulta
                    </button>
                    <button type="button" data-tab="receta" class="tab-btn py-3 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                        Receta
                    </button>
                </nav>
            </div>

            {{-- Tab Consulta --}}
            <div id="tab-consulta" class="tab-content space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico</label>
                    <textarea name="diagnosis" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Describa el diagnóstico del paciente aquí...">{{ old('diagnosis', $consultation->diagnosis ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tratamiento</label>
                    <textarea name="treatment" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Describa el tratamiento recomendado aquí...">{{ old('treatment', $consultation->treatment ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Agregue notas adicionales sobre la consulta.">{{ old('notes', $consultation->notes ?? '') }}</textarea>
                </div>
            </div>

            {{-- Tab Receta --}}
            <div id="tab-receta" class="tab-content hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="table-prescriptions">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Medicamento</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dosis</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Frecuencia / Duración</th>
                                <th class="px-4 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $meds = old('medications');
                                if ($meds === null && $consultation && $consultation->prescriptions->isNotEmpty()) {
                                    $meds = $consultation->prescriptions->keyBy(fn ($p, $i) => $i)->all();
                                }
                                if (!is_array($meds) || empty($meds)) {
                                    $meds = [0 => ['medication' => '', 'dosage' => '', 'frequency' => '']];
                                }
                            @endphp
                            @foreach ($meds as $idx => $med)
                                <tr class="prescription-row">
                                    <td class="px-4 py-2"><input type="text" name="medications[{{ $idx }}][medication]" value="{{ is_object($med) ? $med->medication : ($med['medication'] ?? '') }}" placeholder="Ej. Amoxicilina 500mg" class="w-full rounded border-gray-300 text-sm"></td>
                                    <td class="px-4 py-2"><input type="text" name="medications[{{ $idx }}][dosage]" value="{{ is_object($med) ? $med->dosage : ($med['dosage'] ?? '') }}" placeholder="Ej. 1 tableta" class="w-full rounded border-gray-300 text-sm"></td>
                                    <td class="px-4 py-2"><input type="text" name="medications[{{ $idx }}][frequency]" value="{{ is_object($med) ? $med->frequency : ($med['frequency'] ?? '') }}" placeholder="Ej. cada 8 horas por 7 días" class="w-full rounded border-gray-300 text-sm"></td>
                                    <td class="px-4 py-2"><button type="button" class="remove-medication text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" id="add-medication" class="mt-3 inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-plus mr-2"></i> Añadir Medicamento
                </button>
            </div>

            <div class="mt-8 flex justify-end">
                <x-wire-button type="submit" blue>
                    <i class="fa-solid fa-save mr-2"></i> Guardar Consulta
                </x-wire-button>
            </div>
        </form>
    </x-wire-card>

    {{-- Modal: Historia médica del paciente --}}
    <div id="modal-historia" tabindex="-1" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-hidden="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500/75" data-modal-hide="modal-historia"></div>
            <div class="relative bg-white rounded-xl shadow-lg max-w-lg w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Historia médica del paciente</h3>
                    <button type="button" data-modal-hide="modal-historia" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-times text-xl"></i></button>
                </div>
                <dl class="space-y-3 text-sm">
                    <div><dt class="font-medium text-gray-500">Tipo de sangre</dt><dd class="text-gray-900">{{ $patient->bloodType->name ?? 'No registrado' }}</dd></div>
                    <div><dt class="font-medium text-gray-500">Alergias</dt><dd class="text-gray-900">{{ $patient->allergies ?: 'No registradas' }}</dd></div>
                    <div><dt class="font-medium text-gray-500">Enfermedades crónicas</dt><dd class="text-gray-900">{{ $patient->chronic_diseases ?: 'No registradas' }}</dd></div>
                    <div><dt class="font-medium text-gray-500">Antecedentes quirúrgicos</dt><dd class="text-gray-900">{{ $patient->surgery_history ?: 'No registrados' }}</dd></div>
                </dl>
                <div class="mt-6">
                    <a href="{{ route('admin.patients.show', $patient) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Ver / Editar Historia Médica</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Consultas anteriores --}}
    <div id="modal-consultas-anteriores" tabindex="-1" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-hidden="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500/75" data-modal-hide="modal-consultas-anteriores"></div>
            <div class="relative bg-white rounded-xl shadow-lg max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-bold text-gray-900">Consultas Anteriores</h3>
                    <button type="button" data-modal-hide="modal-consultas-anteriores" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-times text-xl"></i></button>
                </div>
                <div class="p-6 overflow-y-auto flex-1">
                    @forelse($previousConsultations as $prev)
                        @php $apt = $prev->appointment; @endphp
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <p class="text-sm font-medium text-gray-900">{{ $apt->appointment_date->format('d/m/Y') }} a las {{ is_string($apt->start_time) ? substr($apt->start_time, 0, 5) : $apt->start_time->format('H:i') }}</p>
                            <p class="text-xs text-gray-500">Atendido por: {{ $apt->doctor->user->name }}</p>
                            <p class="text-sm text-gray-700 mt-2"><strong>Diagnóstico:</strong> {{ Str::limit($prev->diagnosis ?? '—', 80) }}</p>
                            @if($prev->treatment)<p class="text-sm text-gray-600 mt-1"><strong>Tratamiento:</strong> {{ Str::limit($prev->treatment, 100) }}</p>@endif
                            @if($prev->notes)<p class="text-sm text-gray-600 mt-1"><strong>Notas:</strong> {{ Str::limit($prev->notes, 80) }}</p>@endif
                            <a href="{{ route('admin.consultations.show', $apt) }}" class="inline-block mt-3 text-indigo-600 hover:text-indigo-800 font-medium text-sm">Consultar Detalle</a>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No hay consultas anteriores para este paciente.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var tabConsulta = document.getElementById('tab-consulta');
            var tabReceta = document.getElementById('tab-receta');
            document.querySelectorAll('.tab-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var tab = this.dataset.tab;
                    document.querySelectorAll('.tab-btn').forEach(function(b) {
                        b.classList.remove('text-indigo-600', 'border-indigo-500');
                        b.classList.add('border-transparent', 'text-gray-500');
                    });
                    this.classList.add('text-indigo-600', 'border-indigo-500');
                    this.classList.remove('border-transparent', 'text-gray-500');
                    tabConsulta.classList.toggle('hidden', tab !== 'consulta');
                    tabReceta.classList.toggle('hidden', tab !== 'receta');
                });
            });

            var tableBody = document.querySelector('#table-prescriptions tbody');
            var rowIndex = tableBody.querySelectorAll('.prescription-row').length;
            document.getElementById('add-medication').addEventListener('click', function() {
                var tr = document.createElement('tr');
                tr.className = 'prescription-row';
                tr.innerHTML = '<td class="px-4 py-2"><input type="text" name="medications[' + rowIndex + '][medication]" placeholder="Ej. Amoxicilina 500mg" class="w-full rounded border-gray-300 text-sm"></td>' +
                    '<td class="px-4 py-2"><input type="text" name="medications[' + rowIndex + '][dosage]" placeholder="Ej. 1 cada 8 horas" class="w-full rounded border-gray-300 text-sm"></td>' +
                    '<td class="px-4 py-2"><input type="text" name="medications[' + rowIndex + '][frequency]" placeholder="Ej. cada 8 horas por 7 dias" class="w-full rounded border-gray-300 text-sm"></td>' +
                    '<td class="px-4 py-2"><button type="button" class="remove-medication text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button></td>';
                tableBody.appendChild(tr);
                rowIndex++;
            });
            tableBody.addEventListener('click', function(e) {
                if (e.target.closest('.remove-medication')) e.target.closest('tr').remove();
            });

            document.querySelectorAll('[data-modal-toggle]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id = this.dataset.modalTarget;
                    document.getElementById(id).classList.remove('hidden');
                });
            });
            document.querySelectorAll('[data-modal-hide]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id = this.dataset.modalHide;
                    if (id) document.getElementById(id).classList.add('hidden');
                    else this.closest('[id^="modal-"]').classList.add('hidden');
                });
            });
        })();
    </script>
</x-admin-layout>
