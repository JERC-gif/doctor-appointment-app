<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.patients.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $patient->load(['user', 'bloodType']);
        return view('admin.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $bloodTypes = BloodType::pluck('name', 'id')->toArray();
        return view('admin.patients.edit', compact('patient', 'bloodTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Patient $patient)
{
    $request->validate([
        'blood_type_id' => 'nullable|exists:blood_types,id',
        'allergies' => 'nullable|string|min:3|max:255',
        'chronic_conditions' => 'nullable|string|min:3|max:255',
        'surgical_history' => 'nullable|string|min:3|max:255',
        'family_history' => 'nullable|string|min:3|max:255',
        'observations' => 'nullable|string|min:3|max:255',
        'emergency_contact_name' => 'nullable|string|min:3|max:255',
        'emergency_contact_phone' => ['nullable', 'regex:/^\(\d{3}\) \d{3}-\d{4}$/', 'size:14'],
        'emergency_contact_relationship' => 'nullable|string|min:3|max:50',
    ]);

    // Limpiar el teléfono antes de guardarlo (quitar paréntesis, espacios y guiones)
    $data = $request->only([
        'blood_type_id',
        'allergies',
        'chronic_conditions',
        'surgical_history',
        'family_history',
        'observations',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
    ]);

    // Limpiar el teléfono
    if ($request->emergency_contact_phone) {
        $data['emergency_contact_phone'] = preg_replace('/\D/', '', $request->emergency_contact_phone);
    }

    $patient->update($data);

    session()->flash('swal', [
        'icon'  => 'success',
        'title' => 'Paciente actualizado',
        'text'  => 'La información del paciente ha sido actualizada exitosamente'
    ]);

    return redirect()->route('admin.patients.edit', $patient);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        // Eliminar el usuario (el paciente se eliminará en cascada)
        $patient->user->delete();

        session()->flash('swal',[
            'icon' => 'success',
            'title' => 'Paciente eliminado',
            'text' => 'El paciente ha sido eliminado exitosamente.'
        ]);

        return redirect()->route('admin.patients.index');
    }
}