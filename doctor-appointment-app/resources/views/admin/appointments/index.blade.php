{{-- Listado de citas médicas --}}
<x-admin-layout
    title="Citas | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas'],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.appointments.create') }}" blue>
            <i class="fa-solid fa-plus"></i>
            Nuevo
        </x-wire-button>
    </x-slot>

    @if (session('offer_send_daily_report'))
        <div
            class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-gray-800 dark:border-blue-900/50 dark:bg-blue-950/40 dark:text-gray-100"
            role="status"
        >
            <p class="mb-3 font-medium">
                ¿Enviar ahora el reporte diario de citas de hoy por correo?
            </p>
            <p class="mb-3 text-gray-600 dark:text-gray-300">
                Incluye el listado para administradores (Mailtrap y Gmail configurado) y para cada doctor con citas el día de hoy.
            </p>
            <form
                method="POST"
                action="{{ route('admin.appointments.send-daily-report') }}"
                class="inline"
            >
                @csrf
                <x-wire-button type="submit" green>
                    <i class="fa-solid fa-envelope"></i>
                    Enviar reporte del día
                </x-wire-button>
            </form>
        </div>
    @endif

    @livewire('admin.datatables.appointment-table')
</x-admin-layout>
