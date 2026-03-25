<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #2563eb; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .details { background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Confirmación de Cita Médica</h1>
    </div>
    <div class="content">
        <p>Se ha registrado exitosamente una cita médica con los siguientes datos:</p>
        <div class="details">
            <p><strong>Paciente:</strong> {{ $appointment->patient?->user?->name ?? 'N/A' }}</p>
            <p><strong>Doctor:</strong> {{ $appointment->doctor?->user?->name ?? 'N/A' }}</p>
            <p><strong>Fecha:</strong> {{ $appointment->appointment_date?->format('d/m/Y') ?? \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</p>
            <p><strong>Hora inicio:</strong> {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}</p>
        </div>
        <p>Se adjunta el comprobante en formato PDF.</p>
    </div>
    <div class="footer">
        <p>Este es un correo automático del sistema de citas médicas.</p>
    </div>
</body>
</html>
