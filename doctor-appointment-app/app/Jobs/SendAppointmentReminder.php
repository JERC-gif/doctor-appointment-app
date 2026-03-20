<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment
    ) {}

    public function handle(WhatsAppService $whatsAppService): void
    {
        if ($this->appointment->status !== 'programado') {
            return;
        }

        $phone = $this->appointment->patient_phone;
        if (!$phone) {
            return;
        }

        $dateStr = Carbon::parse($this->appointment->appointment_date)->format('d/m/Y');
        $timeStr = substr($this->appointment->start_time, 0, 5);
        $patientName = $this->appointment->patient_name ?? 'Paciente';

        $message = sprintf(
            '⏰ Recordatorio: Hola %s, mañana tienes cita médica el %s a las %s. Si necesitas cancelar, llámanos con anticipación.',
            $patientName,
            $dateStr,
            $timeStr
        );

        $whatsAppService->send($phone, $message);
    }
}
