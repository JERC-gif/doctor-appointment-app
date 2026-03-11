<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\AppointmentAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Módulo de calendarios: vista mensual con citas en cada día (hora + paciente + punto de color).
 */
class CalendarController extends Controller
{
    public function __construct(
        protected AppointmentAvailabilityService $availabilityService
    ) {}

    /**
     * Vista principal: selector de doctor, mes con citas en celdas, navegación y detalle del día.
     */
    public function index(Request $request)
    {
        $doctors = Doctor::with('user')->orderBy('id')->get();
        $doctorId = $request->get('doctor_id', $doctors->first()?->id);
        $currentDoctor = $doctors->firstWhere('id', (int) $doctorId) ?? $doctors->first();

        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);
        $selectedDate = $request->get('date');

        $date = Carbon::createFromDate($year, $month, 1);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        // Semana empieza en domingo (dom, lun, ..., sáb)
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        $weeks = [];
        $day = $startOfCalendar->copy();
        while ($day->lte($endOfCalendar)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $week[] = [
                    'date' => $day->copy(),
                    'isCurrentMonth' => $day->month === $month,
                    'isToday' => $day->isToday(),
                ];
                $day->addDay();
            }
            $weeks[] = $week;
        }

        // Citas del mes por fecha (para el doctor seleccionado o todas)
        $appointmentsByDate = [];
        if ($currentDoctor) {
            $appointments = Appointment::query()
                ->where('doctor_id', $currentDoctor->id)
                ->whereBetween('appointment_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                ->with('patient.user')
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->get();
            foreach ($appointments as $apt) {
                $d = $apt->appointment_date->format('Y-m-d');
                if (!isset($appointmentsByDate[$d])) {
                    $appointmentsByDate[$d] = [];
                }
                $appointmentsByDate[$d][] = $apt;
            }
        }

        $daySlots = [];
        if ($currentDoctor && $selectedDate) {
            $daySlots = $this->availabilityService->getDayViewSlots($currentDoctor->id, $selectedDate);
        }

        return view('admin.calendar.index', [
            'doctors' => $doctors,
            'currentDoctor' => $currentDoctor,
            'year' => $year,
            'month' => $month,
            'monthName' => $date->locale('es')->monthName,
            'weeks' => $weeks,
            'selectedDate' => $selectedDate,
            'daySlots' => $daySlots,
            'appointmentsByDate' => $appointmentsByDate,
        ]);
    }
}
