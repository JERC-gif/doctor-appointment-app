<x-admin-layout
    title="Soporte | simify"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Soporte',
        ],
    ]">

    <x-slot name="action">
        <x-wire-button blue href="{{ route('admin.tickets.create') }}">
            <i class="fa-solid fa-plus"></i>
            Nuevo Ticket
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.support-ticket-table')

    @push('js')
        {{-- Alerta SweetAlert2 cuando hay un mensaje flash --}}
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

        {{-- Confirmación de eliminación con SweetAlert2 --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.delete-ticket-form').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: 'El ticket será eliminado permanentemente.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
</x-admin-layout>

