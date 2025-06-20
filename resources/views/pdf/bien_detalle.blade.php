<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Bien</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            margin: 30px;
            background-color: #ffffff;
            text-align: center;
        }
        .contenedor {
            width: 85%;
            margin: 0 auto;
            text-align: left;
            background: #ffffff;
            padding: 20px;
        }
        h1 {
            color: #2C3E50;
            font-size: 24px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-top: 15px;
        }
        td {
            padding: 10px;
            vertical-align: top;
        }
        td:first-child {
            font-weight: bold;
            color: #34495E;
            text-transform: uppercase;
            width: 40%;
        }
        td:last-child {
            background-color: #ECF0F1;
            border-radius: 5px;
            font-weight: bold;
            padding-left: 10px;
        }
        .qr {
            text-align: center;
            margin-top: 25px;
        }
        .qr img {
            width: 140px;
            height: 140px;
        }
        .logo {
            width: 250px; /* Aumenta el tamaño del logo */
            height: auto;
            display: block;
            margin: 0 auto 20px auto;
        }
    </style>
</head>
<body>
    <!-- Logo con Base64 para asegurar que se muestre -->
    @if($logoBase64)
        <img src="{{ $logoBase64 }}" alt="Logo Universidad" class="logo">
    @endif

    <div class="contenedor">
        <h1>Detalles del Bien</h1>

        <table>
            <tr>
                <td>N° de Inventario:</td>
                <td>{{ $bien->numero_inventario }}</td>
            </tr>
            <tr>
                <td>N° de Serie:</td>
                <td>{{ $bien->numero_serie }}</td>
            </tr>
            <tr>
                <td>Nombre del Bien:</td>
                <td>{{ $bien->nombre }}</td>
            </tr>
            <tr>
                <td>Estado del Bien:</td>
                <td>{{ ucfirst($bien->estado) }}</td>
            </tr>
            <tr>
                <td>Descripción General:</td>
                <td>{!! nl2br(e($bien->descripcion_general)) !!}</td>
            </tr>
            <tr>
                <td>Observaciones:</td>
                <td>{!! nl2br(e($bien->observaciones)) !!}</td>
            </tr>
            <tr>
                <td>Fecha de Adquisición:</td>
                <td>{{ \Carbon\Carbon::parse($bien->fecha_adquisicion)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Departamento:</td>
                <td>{{ $bien->departamento->nombre ?? 'No asignado' }}</td>
            </tr>
            <tr>
                <td>Categoría:</td>
                <td>{{ $bien->categoria }}</td>
            </tr>
            <tr>
                <td>Resguardante:</td>
                <td>{{ $bien->asignacion->resguardante->nombre_apellido ?? 'No asignado' }}</td>
            </tr>
        </table>

        <!-- Código QR -->
        <div class="qr">
            <h3 style="color: #555; text-transform: uppercase; font-size: 16px;">Código QR del Bien</h3>
            <img src="{{ $qrCodeSvgBase64 }}" alt="Código QR">
        </div>
    </div>
</body>
</html>
