<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Prescription;
use Illuminate\Http\Request;

/**
 * Datos de la consulta (diagnóstico, tratamiento, notas) y receta médica por cita.
 */
class ConsultationController extends Controller
{
    /**
     * Vista de consulta: tabs Consulta y Receta, modales Historia y Consultas Anteriores.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load([
            'patient.user',
            'patient.bloodType',
            'doctor.user',
            'consultation.prescriptions',
        ]);

        $consultation = $appointment->consultation;

        // Consultas anteriores del mismo paciente (con datos de consulta)
        $previousConsultations = Consultation::query()
            ->whereHas('appointment', fn ($q) => $q->where('patient_id', $appointment->patient_id)->where('id', '!=', $appointment->id))
            ->with(['appointment.doctor.user'])
            ->get()
            ->sortByDesc(fn ($c) => $c->appointment->appointment_date->format('Y-m-d') . ' ' . (is_string($c->appointment->start_time) ? $c->appointment->start_time : $c->appointment->start_time->format('H:i:s')))
            ->values()
            ->take(20);

        return view('admin.consultations.show', compact('appointment', 'consultation', 'previousConsultations'));
    }

    /**
     * Guardar consulta (diagnóstico, tratamiento, notas) y receta (medicamentos).
     */
    public function store(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'diagnosis'  => 'nullable|string|max:10000',
            'treatment'  => 'nullable|string|max:10000',
            'notes'      => 'nullable|string|max:5000',
            'medications' => 'nullable|array',
            'medications.*.medication' => 'nullable|string|max:255',
            'medications.*.dosage'     => 'nullable|string|max:255',
            'medications.*.frequency'  => 'nullable|string|max:255',
        ], [], [
            'diagnosis' => 'diagnóstico',
            'treatment' => 'tratamiento',
            'notes'     => 'notas',
        ]);

        $consultation = $appointment->consultation()->firstOrCreate([], [
            'diagnosis'  => null,
            'treatment'  => null,
            'notes'      => null,
        ]);

        $consultation->update([
            'diagnosis' => $data['diagnosis'] ?? null,
            'treatment' => $data['treatment'] ?? null,
            'notes'     => $data['notes'] ?? null,
        ]);

        $consultation->prescriptions()->delete();
        $medications = $data['medications'] ?? [];
        foreach ($medications as $row) {
            if (empty($row['medication']) && empty($row['dosage'])) {
                continue;
            }
            Prescription::create([
                'consultation_id' => $consultation->id,
                'medication'      => $row['medication'] ?? '',
                'dosage'          => $row['dosage'] ?? '',
                'frequency'       => $row['frequency'] ?? null,
            ]);
        }

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Consulta guardada',
            'text'  => 'Los datos de la consulta y la receta se han guardado correctamente.',
        ]);

        return redirect()->route('admin.consultations.show', $appointment);
    }
}
