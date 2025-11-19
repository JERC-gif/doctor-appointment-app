@php
    $id = $userId ?? (isset($user) ? $user->id : null);
@endphp
@if($id)
<div class="flex items-center space-x-2">
    <!-- Botón Editar -->
    <x-wire-button href="{{ route('admin.users.edit', ['user' => $id]) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    <!-- Botón Eliminar -->
    <form action="{{ route('admin.users.destroy', ['user' => $id]) }}" method="POST" class="delete-form">
        @csrf
        @method('DELETE')

        <x-wire-button type="submit" red xs>
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>
@endif

