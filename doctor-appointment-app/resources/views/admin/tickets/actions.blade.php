{{-- Botones de acción por fila en la tabla de tickets de soporte --}}
<div class="flex items-center gap-2">
    <x-wire-button href="{{ route('admin.tickets.show', $ticket) }}" blue xs>
        <i class="fa-solid fa-eye"></i>
    </x-wire-button>

    <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="delete-form">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs>
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>
