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

    <!-- Botón Eliminar (Deshabilitado con advertencia) -->
    <x-wire-button 
        type="button" 
        red 
        xs
        onclick="Swal.fire({
            icon: 'warning',
            title: 'Acción no permitida',
            text: 'No se puede eliminar usuarios en este momento.',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#d33'
        })"
    >
        <i class="fa-solid fa-trash"></i>
    </x-wire-button>
</div>
@endif


