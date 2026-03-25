<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyAppointmentReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $appointments,
        public $date,
        public string $recipientType = 'admin'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reporte Diario de Citas - '.Carbon::parse($this->date)->format('d/m/Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-report',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.daily-appointments-report', [
            'appointments' => $this->appointments,
            'date' => $this->date,
            'recipientType' => $this->recipientType,
        ]);

        $slug = Carbon::parse($this->date)->format('Y-m-d');
        $suffix = $this->recipientType === 'admin' ? 'admin' : 'doctor';
        $filename = "reporte-citas-{$slug}-{$suffix}.pdf";

        return [
            Attachment::fromData(fn () => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }
}
