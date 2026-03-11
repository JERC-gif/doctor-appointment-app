{{-- Botones de acción: consulta (datos/receta), editar, eliminar --}}
<div class="flex items-center gap-2">
    <x-wire-button href="{{ route('admin.consultations.show', $appointment) }}" green xs title="Datos de la consulta">
        <i class="fa-solid fa-stethoscope"></i>
    </x-wire-button>
    <x-wire-button href="{{ route('admin.appointments.edit', $appointment) }}" blue xs title="Editar cita">
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>
    <form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST" class="delete-form inline">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs title="Eliminar">
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>
