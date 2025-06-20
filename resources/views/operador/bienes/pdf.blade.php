<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }
        .details {
            width: 60%;
        }
        .qr-code {
            width: 30%;
            text-align: center;
        }
        h3 {
            text-align: center;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .info-row label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h3>Detalles del Bien</h3>
    <div class="container">
        <div class="details">
            <div class="info-row"><label>N° de Inventario:</label> {{ $bien->numero_inventario }}</div>
            <div class="info-row"><label>N° de Serie:</label> {{ $bien->numero_serie }}</div>
            <div class="info-row"><label>Nombre:</label> {{ $bien->nombre }}</div>
            <div class="info-row"><label>Estado del Bien:</label> {{ $bien->estado }}</div>
            <div class="info-row"><label>Descripción General:</label> {{ $bien->descripcion_general }}</div>
            <div class="info-row"><label>Observaciones:</label> {{ $bien->observaciones }}</div>
            <div class="info-row"><label>Fecha de Adquisición del bien:</label> {{ $bien->fecha_adquisicion }}</div>
            <div class="info-row"><label>Área:</label> {{ $bien->area }}</div>
            <div class="info-row"><label>Categoría:</label> {{ $bien->categoria }}</div>
            <div class="info-row"><label>Resguardante:</label> {{ $bien->asignacion->resguardante->nombre_apellido ?? 'N/A' }}</div>
        </div>
        <div class="qr-code">
            <label>Código QR del Bien:</label>
            {!! $qrCode !!}
        </div>
    </div>
</body>
</html>
