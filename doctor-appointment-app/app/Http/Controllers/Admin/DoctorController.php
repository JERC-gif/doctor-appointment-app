<?php

// CRUD para la gestión de doctores en el panel de administración

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    // Muestra el listado de doctores
    public function index()
    {
        return view('admin.doctors.index');
    }

    // Formulario para crear un nuevo doctor
    // Solo lista usuarios con rol Doctor que aún no tienen perfil médico
    public function create()
    {
        // El rol en la BD está en minúsculas: 'doctor'
        $users = User::role('doctor')
            ->whereDoesntHave('doctor')
            ->get();
        $specialities = Speciality::all();
        return view('admin.doctors.create', compact('users', 'specialities'));
    }

    // Almacena el nuevo doctor tras validar los datos
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'                => 'required|exists:users,id|unique:doctors,user_id',
            'speciality_id'          => 'nullable|exists:specialities,id',
            'medical_license_number' => 'nullable|string|max:255',
            'biography'              => 'nullable|string',
        ], [], [
            'user_id'                => 'usuario',
            'speciality_id'          => 'especialidad',
            'medical_license_number' => 'número de licencia médica',
            'biography'              => 'biografía',
        ]);

        $doctor = Doctor::create($data);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => '¡Doctor creado!',
            'text'  => 'El doctor se ha creado correctamente.',
        ]);

        return redirect()->route('admin.doctors.edit', $doctor);
    }

    // Formulario de edición del perfil médico
    public function edit(string $id)
    {
        $doctor       = Doctor::findOrFail($id);
        $specialities = Speciality::all();
        return view('admin.doctors.edit', compact('doctor', 'specialities'));
    }

    // Actualiza los datos del doctor (no se puede cambiar el usuario asociado)
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'speciality_id'          => 'nullable|exists:specialities,id',
            'medical_license_number' => 'nullable|string|max:255',
            'biography'              => 'nullable|string|max:255',
        ], [], [
            'speciality_id'          => 'especialidad',
            'medical_license_number' => 'número de licencia médica',
            'biography'              => 'biografía',
        ]);

        $doctor = Doctor::findOrFail($id);
        $doctor->update($data);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => '¡Doctor actualizado!',
            'text'  => 'Los datos del doctor se han actualizado correctamente.',
        ]);

        return redirect()->route('admin.doctors.edit', $doctor); //Index o Edit 
    }

    // Elimina el perfil médico del doctor (no elimina el usuario)
    public function destroy(string $id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => '¡Doctor eliminado!',
            'text'  => 'El perfil médico ha sido eliminado correctamente.',
        ]);

        return redirect()->route('admin.doctors.index');
    }
}
