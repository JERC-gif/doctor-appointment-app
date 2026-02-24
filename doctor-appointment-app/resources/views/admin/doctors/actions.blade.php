{{-- Botones de acción por fila en la tabla de doctores --}}
<div class="flex items-center gap-2">
    <x-wire-button href="{{ route('admin.doctors.edit', $doctor) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    {{-- La clase 'delete-form' activa el confirm de SweetAlert2 definido en el layout --}}
    <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" class="delete-form">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs>
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>
