<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1e293b;
        }

        h1 {
            margin-bottom: 4px;
            font-size: 20px;
            color: #0f172a;
        }

        .subtitle {
            margin-bottom: 18px;
            font-size: 11px;
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #1e293b;
            color: white;
        }

        th {
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        td {
            padding: 7px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <h1>{{ $titulo }}</h1>

    <div class="subtitle">
        Desde {{ $inicio->format('d/m/Y') }} hasta {{ $fin->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                @foreach ($columnas as $columna)
                    <th>{{ $columna }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @forelse ($filas as $fila)
                <tr>
                    @foreach ($fila as $valor)
                        <td>{{ $valor }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columnas) }}">
                        No existen registros para el periodo seleccionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Reporte generado el {{ now()->format('d/m/Y H:i') }}.
    </div>

</body>
</html>