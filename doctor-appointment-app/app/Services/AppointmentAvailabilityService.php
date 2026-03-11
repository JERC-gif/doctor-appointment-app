<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Resuelve disponibilidad y conflictos de horarios para citas.
 * Citas de 30 minutos; disponibilidad guardada en bloques de 15 min.
 */
class AppointmentAvailabilityService
{
    /** Duración de la cita en minutos */
    public const APPOINTMENT_DURATION_MINUTES = 30;

    /**
     * Obtiene slots disponibles para una fecha (y opcionalmente especialidad).
     * Un slot es disponible si: (1) el doctor tiene esos 15-min en su horario y
     * (2) no hay cita que solape con el slot de 30 min.
     *
     * @return Collection<int, array{doctor_id: int, doctor: Doctor, start_time: string, end_time: string}>
     */
    public function getAvailableSlots(string $date, ?int $specialityId = null): Collection
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeekIso; // 1 = Lunes, 7 = Domingo

        $doctors = Doctor::query()
            ->with(['user', 'speciality', 'availability'])
            ->when($specialityId, fn ($q) => $q->where('speciality_id', $specialityId))
            ->get();

        $results = collect();

        foreach ($doctors as $doctor) {
            $slots = $this->getAvailableSlotsForDoctor($doctor, $dayOfWeek, $date);
            foreach ($slots as $slot) {
                $results->push([
                    'doctor_id'   => $doctor->id,
                    'doctor'     => $doctor,
                    'start_time' => $slot['start'],
                    'end_time'   => $slot['end'],
                ]);
            }
        }

        return $results->sortBy(['start_time', 'doctor_id'])->values();
    }

    /**
     * Slots de 30 min disponibles para un doctor en un día de la semana y fecha.
     * Requiere dos bloques consecutivos de 15 min en availability.
     */
    private function getAvailableSlotsForDoctor(Doctor $doctor, int $dayOfWeek, string $date): array
    {
        $availability = $doctor->availability()
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();

        $starts = $availability->map(function ($a) {
            $t = $a->start_time;
            return is_string($t) ? $t : $t->format('H:i:s');
        })->values()->all();

        $slots30 = [];
        for ($i = 0; $i < count($starts) - 1; $i++) {
            $start = $starts[$i];
            $next  = $starts[$i + 1];
            if ($this->minutesBetween($start, $next) === 15) {
                $end = $this->addMinutesToTime($start, self::APPOINTMENT_DURATION_MINUTES);
                if (!$this->hasConflict($doctor->id, $date, $start, $end)) {
                    $slots30[] = ['start' => $start, 'end' => $end];
                }
            }
        }

        return $slots30;
    }

    /** Hay alguna cita del doctor en esa fecha que solape con [start, end] */
    public function hasConflict(int $doctorId, string $date, string $start, string $end): bool
    {
        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->whereNotIn('status', [Appointment::STATUS_CANCELADO])
            ->whereRaw('start_time < ? AND end_time > ?', [$end, $start])
            ->exists();
    }

    /** Valida que al crear/actualizar una cita no haya solapamiento (para el mismo doctor/fecha). */
    public function validateNoConflict(int $doctorId, string $date, string $start, string $end, ?int $excludeAppointmentId = null): bool
    {
        $q = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->whereNotIn('status', [Appointment::STATUS_CANCELADO])
            ->whereRaw('start_time < ? AND end_time > ?', [$end, $start]);

        if ($excludeAppointmentId !== null) {
            $q->where('id', '!=', $excludeAppointmentId);
        }

        return !$q->exists();
    }

    private function minutesBetween(string $start, string $end): int
    {
        $s = Carbon::createFromFormat('H:i:s', strlen($start) === 5 ? $start . ':00' : $start);
        $e = Carbon::createFromFormat('H:i:s', strlen($end) === 5 ? $end . ':00' : $end);
        return (int) $s->diffInMinutes($e);
    }

    private function addMinutesToTime(string $time, int $minutes): string
    {
        $t = strlen($time) === 5 ? $time . ':00' : $time;
        $c = Carbon::createFromFormat('H:i:s', $t)->addMinutes($minutes);
        return $c->format('H:i:s');
    }

    /**
     * Vista de un día para el calendario: slots de 30 min con estado para colorear.
     * Verde = disponible (en disponibilidad y sin cita), Gris = ocupado (tiene cita), Rojo = no disponible.
     *
     * @return array<int, array{start: string, end: string, status: string, appointment: ?Appointment}>
     */
    public function getDayViewSlots(int $doctorId, string $date): array
    {
        $doctor = Doctor::with(['availability', 'appointments'])->find($doctorId);
        if (!$doctor) {
            return [];
        }

        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeekIso;
        $availability = $doctor->availability()
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();

        $starts15 = $availability->map(function ($a) {
            $t = $a->start_time;
            return is_string($t) ? substr($t, 0, 5) : $t->format('H:i');
        })->values()->all();

        $appointments = $doctor->appointments()
            ->where('appointment_date', $date)
            ->whereNotIn('status', [Appointment::STATUS_CANCELADO])
            ->with('patient.user')
            ->get();

        $slots = [];
        for ($h = 8; $h < 18; $h++) {
            for ($m = 0; $m < 60; $m += 30) {
                $start = sprintf('%02d:%02d', $h, $m);
                $end = $m === 30 ? sprintf('%02d:%02d', $h + 1, 0) : sprintf('%02d:%02d', $h, 30);
                if ($end === '18:00') {
                    $end = '18:00';
                }
                $startSec = $start . ':00';
                $endSec = $end . ':00';
                $hasAvailability = $this->doctorHasConsecutive15($starts15, $start);
                $appointment = $appointments->first(function ($apt) use ($startSec, $endSec) {
                    $aptStart = is_string($apt->start_time) ? $apt->start_time : $apt->start_time->format('H:i:s');
                    $aptEnd = is_string($apt->end_time) ? $apt->end_time : $apt->end_time->format('H:i:s');
                    return $aptStart < $endSec && $aptEnd > $startSec;
                });

                if ($appointment) {
                    $status = 'occupied';
                } elseif ($hasAvailability) {
                    $status = 'available';
                } else {
                    $status = 'unavailable';
                }

                $slots[] = [
                    'start'      => $start,
                    'end'        => $end,
                    'status'     => $status,
                    'appointment' => $appointment,
                ];
            }
        }

        return $slots;
    }

    /** Comprueba si el doctor tiene dos bloques de 15 min consecutivos desde $start (para 30 min). */
    private function doctorHasConsecutive15(array $starts15, string $slotStart): bool
    {
        $slotEnd = $this->addMinutesToTime(strlen($slotStart) === 5 ? $slotStart . ':00' : $slotStart, 30);
        $required = [];
        for ($t = $slotStart . ':00'; $t !== $slotEnd; $t = $this->addMinutesToTime($t, 15)) {
            $required[] = substr($t, 0, 5);
        }
        foreach ($required as $r) {
            if (!in_array($r, $starts15, true)) {
                return false;
            }
        }
        return true;
    }
}
