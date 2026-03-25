<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        .header { text-align: center; border-bottom: 3px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #2563eb; margin: 0; }
        .header p { color: #6b7280; margin: 5px 0 0; }
        .receipt-box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 25px; }
        .field { margin-bottom: 15px; }
        .field label { font-weight: bold; color: #374151; display: block; margin-bottom: 3px; }
        .field span { font-size: 16px; }
        .footer { text-align: center; margin-top: 40px; font-size: 11px; color: #9ca3af; }
        .badge { display: inline-block; background-color: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comprobante de Cita Médica</h1>
        <p>Sistema de Citas - Doctor Appointment App</p>
    </div>

    <div class="receipt-box">
        <div class="field">
            <label>Estado:</label>
            <span class="badge">CONFIRMADA</span>
        </div>
        <div class="field">
            <label>Paciente:</label>
            <span>{{ $appointment->patient?->user?->name ?? 'N/A' }}</span>
        </div>
        <div class="field">
            <label>Correo del Paciente:</label>
            <span>{{ $appointment->patient?->user?->email ?? 'N/A' }}</span>
        </div>
        <div class="field">
            <label>Doctor:</label>
            <span>{{ $appointment->doctor?->user?->name ?? 'N/A' }}</span>
        </div>
        <div class="field">
            <label>Fecha de la Cita:</label>
            <span>{{ $appointment->appointment_date?->format('d/m/Y') ?? \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</span>
        </div>
        <div class="field">
            <label>Hora de inicio:</label>
            <span>{{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}</span>
        </div>
        <div class="field">
            <label>Hora de fin:</label>
            <span>{{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}</span>
        </div>
        <div class="field">
            <label>Fecha de Emisión:</label>
            <span>{{ now()->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Este comprobante fue generado automáticamente. Preséntelo el día de su cita.</p>
    </div>
</body>
</html>
