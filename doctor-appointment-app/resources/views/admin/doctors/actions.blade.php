{{-- Botones de acción por fila en la tabla de doctores: editar, horarios, eliminar --}}
<div class="flex items-center gap-2">
    <x-wire-button href="{{ route('admin.doctors.edit', $doctor) }}" blue xs title="Editar">
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>
    <x-wire-button href="{{ route('admin.doctors.schedule', $doctor) }}" green xs title="Ver horario de este doctor">
        <i class="fa-solid fa-clock"></i>
    </x-wire-button>
    <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" class="delete-form inline">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs title="Eliminar">
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>
