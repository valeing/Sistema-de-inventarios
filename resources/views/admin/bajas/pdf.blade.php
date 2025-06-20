<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente de Baja</title>
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
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .container {
            width: 90%;
            margin: auto;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .detail-table th, .detail-table td {
            border: 1px solid #444;
            padding: 12px;
            text-align: left;
        }
        .detail-table th {
            background-color: #004085;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
        }
        .detail-table td {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Expediente de Baja del Bien</h2>

        <table class="detail-table">
            <tr>
                <th>N.º de Inventario del Bien</th>
                <td>{{ $baja->numero_inventario ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Descripción del Bien</th>
                <td>{{ $baja->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Fecha de Baja</th>
                <td>{{ $baja->fecha_baja }}</td>
            </tr>
            <tr>
                <th>Motivo de Baja</th>
                <td>{{ $baja->motivo }}</td>
            </tr>
            <tr>
                <th>Comentarios</th>
                <td>{{ $baja->descripcion_problema ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Resguardante</th>
                <td>{{ $baja->nombre_apellido ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Categoría</th>
                <td>{{ $baja->categoria ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Departamento</th> <!-- Se reemplaza "Área" por "Departamento" -->
                <td>{{ $baja->departamento ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Estado del Bien</th>
                <td>{{ $baja->estado ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Fecha de Adquisición</th>
                <td>{{ $baja->fecha_adquisicion ?? 'N/A' }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Este documento es generado automáticamente y es de carácter oficial.</p>
        </div>
    </div>
</body>
</html>
