@php
    $statusColors = [
        'abierto'     => 'bg-yellow-100 text-yellow-800',
        'en_progreso' => 'bg-blue-100 text-blue-800',
        'cerrado'     => 'bg-green-100 text-green-800',
    ];
    $statusLabels = [
        'abierto'     => 'Abierto',
        'en_progreso' => 'En Progreso',
        'cerrado'     => 'Cerrado',
    ];
    $priorityColors = [
        'baja'  => 'bg-gray-100 text-gray-800',
        'media' => 'bg-orange-100 text-orange-800',
        'alta'  => 'bg-red-100 text-red-800',
    ];
    $priorityLabels = [
        'baja'  => '🟢 Baja',
        'media' => '🟡 Media',
        'alta'  => '🔴 Alta',
    ];
@endphp

<x-admin-layout
    title="Detalle del Ticket | simify"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Soporte', 'href' => route('admin.tickets.index')],
        ['name' => 'Ticket #' . $ticket->id],
    ]">

    <x-slot name="action">
        <x-wire-button amber href="{{ route('admin.tickets.edit', $ticket) }}">
            <i class="fa-solid fa-pen-to-square"></i>
            Editar
        </x-wire-button>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna izquierda: Info del ticket --}}
        <div class="lg:col-span-1">
            <x-wire-card>
                <div class="text-center mb-4">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-ticket text-blue-600 text-4xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Ticket #{{ $ticket->id }}</h2>
                    <p class="text-gray-500">{{ $ticket->user->name }}</p>
                </div>

                <div class="space-y-3 border-t pt-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-info text-gray-400 w-5"></i>
                        <div>
                            <span class="text-sm text-gray-500">Estado:</span>
                            <p>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColors[$ticket->status] ?? '' }}">
                                    {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-flag text-gray-400 w-5"></i>
                        <div>
                            <span class="text-sm text-gray-500">Prioridad:</span>
                            <p>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $priorityColors[$ticket->priority] ?? '' }}">
                                    {{ $priorityLabels[$ticket->priority] ?? $ticket->priority }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-user text-gray-400 w-5"></i>
                        <div>
                            <span class="text-sm text-gray-500">Creado por:</span>
                            <p class="font-medium">{{ $ticket->user->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope text-gray-400 w-5"></i>
                        <div>
                            <span class="text-sm text-gray-500">Email:</span>
                            <p class="font-medium">{{ $ticket->user->email }}</p>
                        </div>
                    </div>
                </div>
            </x-wire-card>

            {{-- Fechas --}}
            <x-wire-card class="mt-6">
                <div class="space-y-2 text-sm text-gray-500">
                    <div>
                        <i class="fa-solid fa-calendar-plus mr-1"></i>
                        Creado: {{ $ticket->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div>
                        <i class="fa-solid fa-calendar-check mr-1"></i>
                        Actualizado: {{ $ticket->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </x-wire-card>
        </div>

        {{-- Columna derecha: Contenido del ticket --}}
        <div class="lg:col-span-2">
            {{-- Descripción del problema --}}
            <x-wire-card>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    <i class="fa-solid fa-file-lines text-blue-500 mr-2"></i>
                    {{ $ticket->title }}
                </h3>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700 whitespace-pre-line">{{ $ticket->description }}</p>
                </div>
            </x-wire-card>

            {{-- Respuesta del administrador --}}
            <x-wire-card class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    <i class="fa-solid fa-reply text-green-500 mr-2"></i>
                    Respuesta del Administrador
                </h3>

                @if($ticket->admin_response)
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-line">{{ $ticket->admin_response }}</p>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-clock text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-400">Aún no hay respuesta del administrador.</p>
                    </div>
                @endif
            </x-wire-card>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.tickets.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Volver a la lista de tickets
        </a>
    </div>

    @push('js')
        @if (session('swal'))
            <script>
                Swal.fire({
                    icon: "{{ session('swal.icon') }}",
                    title: "{{ session('swal.title') }}",
                    text: "{{ session('swal.text') }}",
                    confirmButtonColor: '#3085d6',
                });
            </script>
        @endif
    @endpush
</x-admin-layout>

