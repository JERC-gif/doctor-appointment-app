<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Speciality;
use App\Services\AppointmentAvailabilityService;
use Illuminate\Http\Request;

/**
 * CRUD de citas médicas. Incluye búsqueda de disponibilidad con resolución de conflictos.
 */
class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentAvailabilityService $availabilityService
    ) {}

    public function index()
    {
        return view('admin.appointments.index');
    }

    /**
     * Muestra el formulario "Buscar disponibilidad" (fecha, hora opcional, especialidad opcional).
     */
    public function create(Request $request)
    {
        $specialities = Speciality::all();
        $date = $request->get('date');
        $specialityId = $request->get('speciality_id') ? (int) $request->get('speciality_id') : null;
        $timeRange = $request->get('time_range'); // formato esperado: "HH:MM-HH:MM"
        $slots = collect();

        $patients = Patient::with('user')->orderBy('id')->get();
        if ($date) {
            $slots = $this->availabilityService->getAvailableSlots($date, $specialityId);

            // Filtrar por rango horario si se envió (opcional)
            if ($timeRange) {
                [$from, $to] = array_pad(explode('-', $timeRange, 2), 2, null);
                $from = $from ? trim($from) : null;
                $to = $to ? trim($to) : null;

                if ($from && $to) {
                    $slots = $slots->filter(function ($slot) use ($from, $to) {
                        $start = $slot['start_time'];
                        $startStr = is_string($start) ? substr($start, 0, 5) : $start->format('H:i');
                        return $startStr >= $from && $startStr < $to;
                    })->values();
                }
            }
        } else {
            $slots = collect();
        }

        return view('admin.appointments.create', compact('specialities', 'date', 'specialityId', 'slots', 'patients', 'timeRange'));
    }

    /**
     * Almacena la cita. Valida que no haya conflicto de horario antes de guardar.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'       => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time'      => 'required|date_format:H:i',
            'end_time'        => 'required|date_format:H:i|after:start_time',
            'reason'          => 'nullable|string|max:5000',
        ], [
            'appointment_date.after_or_equal' => 'La fecha de la cita no puede ser anterior a hoy.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ], [
            'patient_id'       => 'paciente',
            'doctor_id'       => 'doctor',
            'appointment_date' => 'fecha',
            'start_time'      => 'hora inicio',
            'end_time'        => 'hora fin',
            'reason'          => 'motivo de la cita',
        ]);

        $date = $data['appointment_date'];
        $start = strlen($data['start_time']) === 5 ? $data['start_time'] . ':00' : $data['start_time'];
        $end   = strlen($data['end_time']) === 5 ? $data['end_time'] . ':00' : $data['end_time'];

        if (!$this->availabilityService->validateNoConflict($data['doctor_id'], $date, $start, $end)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['start_time' => 'El horario seleccionado ya no está disponible. Elige otro slot.']);
        }

        Appointment::create([
            'patient_id'       => $data['patient_id'],
            'doctor_id'       => $data['doctor_id'],
            'appointment_date' => $date,
            'start_time'      => $start,
            'end_time'        => $end,
            'status'          => Appointment::STATUS_PROGRAMADO,
            'reason'          => $data['reason'] ?? null,
        ]);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Cita creada',
            'text'  => 'La cita se ha programado correctamente.',
        ]);

        return redirect()->route('admin.appointments.index');
    }

    public function edit(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user', 'doctor.speciality']);
        $specialities = Speciality::all();
        $patients = Patient::with('user')->get();
        return view('admin.appointments.edit', compact('appointment', 'specialities', 'patients'));
    }

    /**
     * Actualiza la cita. Valida que no haya conflicto si se cambia doctor/fecha/hora.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'       => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time'      => 'required|date_format:H:i',
            'end_time'        => 'required|date_format:H:i|after:start_time',
            'status'          => 'required|in:programado,completado,cancelado',
        ], [
            'appointment_date.after_or_equal' => 'La fecha de la cita no puede ser anterior a hoy.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ], [
            'patient_id'       => 'paciente',
            'doctor_id'       => 'doctor',
            'appointment_date' => 'fecha',
            'start_time'      => 'hora inicio',
            'end_time'        => 'hora fin',
            'status'          => 'estado',
        ]);

        $date = $data['appointment_date'];
        $start = strlen($data['start_time']) === 5 ? $data['start_time'] . ':00' : $data['start_time'];
        $end   = strlen($data['end_time']) === 5 ? $data['end_time'] . ':00' : $data['end_time'];

        if (!$this->availabilityService->validateNoConflict($data['doctor_id'], $date, $start, $end, $appointment->id)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['start_time' => 'El horario seleccionado genera conflicto con otra cita.']);
        }

        $appointment->update([
            'patient_id'       => $data['patient_id'],
            'doctor_id'       => $data['doctor_id'],
            'appointment_date' => $date,
            'start_time'      => $start,
            'end_time'        => $end,
            'status'          => $data['status'],
        ]);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Cita actualizada',
            'text'  => 'Los datos de la cita se han guardado correctamente.',
        ]);

        return redirect()->route('admin.appointments.index');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Cita eliminada',
            'text'  => 'La cita ha sido eliminada correctamente.',
        ]);
        return redirect()->route('admin.appointments.index');
    }
}
