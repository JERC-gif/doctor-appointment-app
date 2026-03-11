<?php

// CRUD para la gestión de doctores en el panel de administración

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
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

    /**
     * Gestor de horarios del doctor: grid de bloques de 15 min por día de la semana.
     */
    public function schedule(string $id)
    {
        $doctor = Doctor::with(['user', 'availability'])->findOrFail($id);
        return view('admin.doctors.schedule', compact('doctor'));
    }

    /**
     * Guarda la disponibilidad del doctor (bloques de 15 min marcados en el grid).
     * Se usa POST para evitar que el body no se envíe con PUT en algunos entornos.
     */
    public function saveSchedule(Request $request, string $id)
    {
        $doctor = Doctor::findOrFail($id);

        $request->validate([
            'slots' => 'nullable|array',
            'slots.*' => 'nullable|array',
        ]);

        $doctor->availability()->delete();

        $slots = $request->input('slots', []);
        if (! is_array($slots)) {
            $slots = [];
        }

        foreach ($slots as $dayOfWeek => $times) {
            if (! is_array($times)) {
                continue;
            }
            $dayOfWeek = (int) $dayOfWeek;
            if ($dayOfWeek < 1 || $dayOfWeek > 7) {
                continue;
            }
            foreach ($times as $startTime => $value) {
                if ($value === '' || $value === null) {
                    continue;
                }
                $startTime = trim((string) $startTime);
                if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $startTime)) {
                    $parts = explode(':', $startTime);
                    $start = sprintf('%02d:%02d:00', (int) $parts[0], (int) ($parts[1] ?? 0));
                    DoctorAvailability::create([
                        'doctor_id'   => $doctor->id,
                        'day_of_week' => $dayOfWeek,
                        'start_time'  => $start,
                    ]);
                }
            }
        }

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Horario guardado',
            'text'  => 'La disponibilidad del doctor se ha actualizado correctamente.',
        ]);

        return redirect()->route('admin.doctors.schedule', $doctor);
    }
}
