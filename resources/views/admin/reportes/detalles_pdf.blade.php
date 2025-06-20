<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalles del Reporte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            font-size: 14px;
        }

        h2 {
            text-align: center;
            color: #333;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }

        .descarga-info {
            text-align: center;
            margin-bottom: 20px;
            font-style: italic;
            font-size: 13px;
        }

        .container {
            width: 90%;
            margin: auto;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
            /* ✅ Fuerza ancho uniforme */
            word-wrap: break-word;
            /* ✅ Permite romper palabras largas */
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #444;
            padding: 12px;
            text-align: left;
            vertical-align: top;
            /* ✅ Alinea el texto arriba si hay saltos */
        }

        .detail-table th {
            background-color: #004085;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
            width: 30%;
        }

        .detail-table td {
            background-color: #f9f9f9;
            width: 70%;
            word-break: break-word;
            /* ✅ Rompe palabras largas dentro de celdas */
        }

        .section-title {
            margin-top: 40px;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 16px;
            color: #004085;
            border-bottom: 2px solid #004085;
            padding-bottom: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #666;
        }
    </style>

</head>

<body>
    <div class="container">
        <h2>Detalles del Reporte y del Bien</h2>

        <p class="descarga-info">Se ha descargado el reporte el día: {{ \Carbon\Carbon::now()->format('Y-m-d ') }}
        </p>

        {{-- Sección del Reporte --}}
        <div class="section-title">Datos del Reporte</div>
        <table class="detail-table">
            <tr>
                <th>Comentario del Resguardante</th>
                <td>{{ $reporte->comentario ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Motivo de Rechazo</th>
                <td>{{ $reporte->comentario_rechazo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Fecha del Reporte</th>
                <td>{{ $reporte->fecha_reporte ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Estado</th>
                <td>{{ ucfirst($reporte->estatus) ?? 'N/A' }}</td>
            </tr>
        </table>

        {{-- Sección del Bien --}}
        <div class="section-title">Datos del Bien</div>
        <table class="detail-table">
            <tr>
                <th>Nombre del Bien</th>
                <td>{{ $bien->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Estado del Bien</th>
                <td>{{ $bien->estado ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Departamento</th>
                <td>{{ $bien->departamento->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Categoría</th>
                <td>{{ $bien->categoria ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Fecha de Adquisición</th>
                <td>{{ $bien->fecha_adquisicion ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>N.º de Serie</th>
                <td>{{ $bien->numero_serie ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>N.º de Inventario</th>
                <td>{{ $bien->numero_inventario ?? 'N/A' }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Este documento es generado automáticamente y es de carácter oficial.</p>
        </div>
    </div>
</body>

</html>
