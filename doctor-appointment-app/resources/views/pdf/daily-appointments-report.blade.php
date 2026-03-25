<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 24px; }
        h1 { color: #1d4ed8; font-size: 18px; margin: 0 0 8px; }
        .meta { color: #6b7280; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background-color: #2563eb; color: #fff; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .summary { background: #eff6ff; padding: 12px; border-radius: 6px; margin-bottom: 16px; }
        .empty { text-align: center; color: #6b7280; padding: 20px; }
        .footer { margin-top: 24px; font-size: 10px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <h1>Reporte diario de citas médicas</h1>
    <p class="meta">Fecha: {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>

    <div class="summary">
        <strong>Total de citas:</strong> {{ $appointments->count() }}<br>
        <strong>Alcance:</strong> {{ $recipientType === 'admin' ? 'Todas las citas del día' : 'Citas del doctor' }}
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
        <p class="empty">No hay citas agendadas para esta fecha.</p>
    @endif

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
