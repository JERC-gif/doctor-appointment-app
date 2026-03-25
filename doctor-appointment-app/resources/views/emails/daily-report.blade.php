<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #2563eb; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #2563eb; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .summary { background-color: #eff6ff; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte Diario de Citas Médicas</h1>
        <p>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
    </div>
    <div class="content">
        <div class="summary">
            <p><strong>Total de citas agendadas para hoy:</strong> {{ $appointments->count() }}</p>
            <p><strong>Tipo de reporte:</strong> {{ $recipientType === 'admin' ? 'Administrador (todas las citas)' : 'Doctor (mis citas)' }}</p>
        </div>

        @if($appointments->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Doctor</th>
                        <th>Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $index => $appointment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $appointment->patient?->user?->name ?? 'N/A' }}</td>
                        <td>{{ $appointment->doctor?->user?->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #6b7280; padding: 20px;">No hay citas agendadas para hoy.</p>
        @endif

        <p style="margin-top: 20px;"><strong>Se adjunta el mismo reporte en formato PDF.</strong></p>
    </div>
    <div class="footer">
        <p>Reporte generado automáticamente a las {{ now()->format('H:i') }} hrs.</p>
    </div>
</body>
</html>
