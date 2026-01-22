<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'id_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ]);


        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'id_number' => $request->id_number,
            'phone' => $request->phone,
            'address' => $request->address,

        ]);

        session() -> flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario Creado',
            'text' => 'El usuario ha sido creado exitosamente.'
        ]);


        return redirect()->route('admin.users.index')
                         ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'id_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ]);

        $data = $request->only('name', 'email', 'role_id', 'id_number', 'phone', 'address');
        
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario Actualizado',
            'text' => 'El usuario ha sido actualizado exitosamente.'
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Usuario actualizado correctamente.');
    }

   public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No puedes eliminarte a ti mismo.'
            ]);
            abort(403, 'No puedes eliminarte a ti mismo.');
        }

        $user->roles()->detach();
        
        $user->delete();

    session()->flash('swal', [
        'icon' => 'success',
        'title' => 'Usuario Eliminado',
        'text' => 'El usuario ha sido eliminado exitosamente.'
    ]);

    return redirect()->route('admin.users.index')
                     ->with('success', 'Usuario eliminado correctamente.');
    } 
}
