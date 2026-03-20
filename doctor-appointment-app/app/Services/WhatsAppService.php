<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsAppService
{
    protected Client $client;
    protected string $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->from = config('services.twilio.from');
    }

    public function send(string $to, string $message): void
    {
        $phone = preg_replace('/\D+/', '', $to);

        if (strlen($phone) === 10) {
            $phone = '521' . $phone;
        } elseif (strlen($phone) === 12 && str_starts_with($phone, '52')) {
            $phone = '521' . substr($phone, 2);
        }

        $toWhatsApp = 'whatsapp:+' . $phone;

        try {
            $this->client->messages->create($toWhatsApp, [
                'from' => $this->from,
                'body' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp (Twilio) error: ' . $e->getMessage(), [
                'to' => $toWhatsApp,
                'exception' => $e,
            ]);
            throw $e;
        }
    }
}
