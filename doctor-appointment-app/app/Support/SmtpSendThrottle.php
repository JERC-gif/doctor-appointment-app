<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Mailtrap (plan testing) limita correos por segundo; espaciar envíos evita 550 Too many emails per second.
 * Plan Mailtrap "testing": pausa generosa + reintentos con backoff.
 */
final class SmtpSendThrottle
{
    private const MAILTRAP_DEFAULT_DELAY_MS = 12000;

    public static function pauseAfterMailer(?string $mailerName = null): void
    {
        self::sleepForMailer($mailerName);
    }

    /**
     * Ejecuta un envío SMTP y reintenta si Mailtrap (u otro) responde 550 por límite de velocidad.
     *
     * @param  callable(): void  $send
     */
    public static function sendWithBackoff(callable $send, ?string $mailerName = null, int $maxAttempts = 6): void
    {
        $mailerName ??= (string) config('mail.default');

        for ($i = 0; $i < $maxAttempts; $i++) {
            if ($i > 0) {
                $wait = 15 + ($i * 5);
                Log::warning('SMTP límite de velocidad; esperando y reintentando.', [
                    'mailer' => $mailerName,
                    'wait_seconds' => $wait,
                    'attempt' => $i + 1,
                    'max' => $maxAttempts,
                ]);
                sleep($wait);
            }

            try {
                $send();

                return;
            } catch (Throwable $e) {
                if (! self::isSmtpRateLimitError($e) || $i === $maxAttempts - 1) {
                    throw $e;
                }
            }
        }
    }

    private static function isSmtpRateLimitError(Throwable $e): bool
    {
        $current = $e;
        while ($current !== null) {
            $msg = $current->getMessage();
            if (str_contains($msg, 'Too many emails') || str_contains($msg, '550 5.7.0')) {
                return true;
            }
            $current = $current->getPrevious();
        }

        return false;
    }

    private static function sleepForMailer(?string $mailerName): void
    {
        $mailerName ??= (string) config('mail.default');
        $host = strtolower((string) config("mail.mailers.{$mailerName}.host", ''));

        $delayMs = (int) config('mail.send_delay_ms_between_smtp_messages', 0);
        if ($delayMs <= 0 && str_contains($host, 'mailtrap')) {
            $delayMs = self::MAILTRAP_DEFAULT_DELAY_MS;
        }

        if ($delayMs > 0) {
            usleep($delayMs * 1000);
        }
    }
}
