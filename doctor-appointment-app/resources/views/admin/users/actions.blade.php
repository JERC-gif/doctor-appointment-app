@php
    $id = $userId ?? (isset($user) ? $user->id : null);
@endphp
@if($id)
<div class="flex items-center space-x-2">
    <!-- Botón Editar -->
    <x-wire-button 
        href="{{ route('admin.users.edit', $id) }}" 
        blue 
        xs
    >
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    <!-- Botón Eliminar -->
    <form action="{{ route('admin.users.destroy', $id) }}" method="POST" class="inline-block" id="delete-form-{{ $id }}">
        @csrf
        @method('DELETE')
        <x-wire-button 
            type="button" 
            red 
            xs
            onclick="confirmDelete({{ $id }})"
        >
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>

<script>
function confirmDelete(userId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + userId).submit();
        }
    });
}
</script>
@endif



