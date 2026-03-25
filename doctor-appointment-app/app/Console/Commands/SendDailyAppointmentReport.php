<?php

namespace App\Console\Commands;

use App\Mail\DailyAppointmentReport;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use App\Support\SmtpSendThrottle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendDailyAppointmentReport extends Command
{
    protected $signature = 'report:daily-appointments';

    protected $description = 'Reporte diario (8:00): admins con rol Administrador reciben todas las citas del día; cada doctor recibe solo las suyas.';

    public function handle(): int
    {
        $today = Carbon::today();

        $allAppointments = Appointment::with(['patient.user', 'doctor.user'])
            ->whereDate('appointment_date', $today)
            ->orderBy('start_time')
            ->get();

        $admins = User::role('Administrador')->get();
        $adminEmails = $admins->pluck('email')->filter()->unique()->values();
        if ($adminEmails->isNotEmpty()) {
            $adminMailable = new DailyAppointmentReport($allAppointments, $today, 'admin');
            $firstAdmin = $adminEmails->shift();
            SmtpSendThrottle::sendWithBackoff(function () use ($adminMailable, $firstAdmin, $adminEmails) {
                if ($adminEmails->isEmpty()) {
                    Mail::to($firstAdmin)->send($adminMailable);
                } else {
                    Mail::to($firstAdmin)->bcc($adminEmails->all())->send($adminMailable);
                }
            });
            SmtpSendThrottle::pauseAfterMailer();
        }

        $gmailForward = trim((string) env('GMAIL_FORWARD_TO', ''));
        if ($gmailForward !== '' && filled(config('mail.mailers.gmail.username')) && filled(config('mail.mailers.gmail.password'))) {
            try {
                $gmailReport = (new DailyAppointmentReport($allAppointments, $today, 'admin'))
                    ->from(
                        (string) config('mail.mailers.gmail.username'),
                        (string) config('mail.from.name')
                    );
                Mail::mailer('gmail')->to($gmailForward)->send($gmailReport);
            } catch (Throwable $e) {
                Log::warning('Reporte diario: copia Gmail no enviada', ['message' => $e->getMessage()]);
                $this->warn('Gmail (copia opcional): '.$e->getMessage());
            }
        }

        $this->info('Reporte enviado a '.$admins->count().' administrador(es).');

        $doctors = Doctor::with('user')->get();
        if ($doctors->contains(fn (Doctor $doctor) => (bool) $doctor->user?->email)) {
            SmtpSendThrottle::pauseAfterMailer();
        }

        $doctorsNotified = 0;
        foreach ($doctors as $doctor) {
            $doctorAppointments = $allAppointments->where('doctor_id', $doctor->id)->values();
            $email = $doctor->user?->email;
            if ($email) {
                SmtpSendThrottle::pauseAfterMailer();
                SmtpSendThrottle::sendWithBackoff(function () use ($doctorAppointments, $today, $email) {
                    Mail::to($email)
                        ->send(new DailyAppointmentReport($doctorAppointments, $today, 'doctor'));
                });
                SmtpSendThrottle::pauseAfterMailer();
                $doctorsNotified++;
            }
        }

        $this->info("Reporte enviado a {$doctorsNotified} doctor(es).");

        return Command::SUCCESS;
    }
}
