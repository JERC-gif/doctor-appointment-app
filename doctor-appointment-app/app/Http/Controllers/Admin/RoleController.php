<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      return view('admin.roles.index');  //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        //Variable de un solo uso para alerta
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol creado correctamente',
            'text' => 'El rol ha sido creado exitosamente'
        ]);

        return redirect()->route('admin.roles.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Buscar el rol
        $role = Role::findOrFail($id);

        // Restricción: No permitir actualizar los primeros 4 roles base del sistema
        if ($role->id <= 4) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Acción no permitida',
                'text' => 'No se puede actualizar un rol base del sistema.'
            ]);

            return redirect()->route('admin.roles.index');
        }

        // Validar campos
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        // Si el campo no cambió, no actualices
        if ($role->name === $request->name) {
            session()->flash('swal', [
                'icon' => 'info',
                'title' => 'Sin cambios',
                'text' => 'No se detectaron modificaciones.'
            ]);

            return redirect()->route('admin.roles.edit', $role);
        }

        // Actualizar el rol
        $role->update($request->only('name'));

        // Variable de un solo uso para alerta de éxito
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol actualizado correctamente',
            'text' => 'El rol ha sido actualizado exitosamente'
        ]);

        // Redireccionar al índice
        return redirect()->route('admin.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Role $role)
    {
        //Restringir la acción para los primeros 4 roles fijos
        if ($role->id <= 4) {
            //Variable de un solo uso
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No puedes eliminar este rol.'
            ]);

            return redirect()->route('admin.roles.index');
        }

        $role->delete();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol eliminado correctamente',
            'text' => 'El rol ha sido eliminado exitosamente'
        ]);

        return redirect()->route('admin.roles.index');
    }
}
