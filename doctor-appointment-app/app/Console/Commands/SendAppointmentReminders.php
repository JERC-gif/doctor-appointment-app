<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Envía recordatorios de WhatsApp para citas de mañana';

    public function handle(WhatsAppService $whatsApp): void
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->where('appointment_date', $tomorrow)
            ->where('status', 'programado')
            ->whereNotNull('patient_phone')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('No hay citas pendientes para mañana.');
            return;
        }

        $count = 0;
        foreach ($appointments as $appointment) {
            try {
                $whatsApp->send(
                    $appointment->patient_phone,
                    sprintf(
                        '⏰ Recordatorio: Hola %s, mañana tienes cita médica el %s a las %s. Si necesitas cancelar, llámanos con anticipación.',
                        $appointment->patient_name ?? 'Paciente',
                        Carbon::parse($appointment->appointment_date)->format('d/m/Y'),
                        substr($appointment->start_time, 0, 5)
                    )
                );
                $this->info("Recordatorio enviado para cita #{$appointment->id}");
                $count++;
            } catch (\Throwable $e) {
                $this->error("Error cita #{$appointment->id}: " . $e->getMessage());
            }
        }

        $this->info("Listo. {$count} recordatorio(s) enviado(s).");
    }
}
