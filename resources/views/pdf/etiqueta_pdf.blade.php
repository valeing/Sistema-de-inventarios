<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiquetas de Bienes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            text-align: center;
            margin: 0;
            padding: 25px 10px;
            /* Ajuste del margen superior e inferior */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: auto;
            table-layout: fixed;
        }

        .etiqueta {
            border: 1px solid black;
            width: 32%;
            height: 230px;
            /* Incrementado para compensar el margen inferior */
            padding: 12px;
            vertical-align: middle;
            text-align: center;
            box-sizing: border-box;
            word-break: break-word;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-evenly;
        }

        .qr {
            width: 100px;
            height: 100px;
            margin-bottom: 5px;
        }

        .texto {
            font-size: 9px;
            margin: 1px 0;
            display: block;
            width: 100%;
            text-align: center;
        }

        .negrita {
            font-weight: bold;
            margin-top: 3px;
            display: block;
            font-size: 10px;
        }

        .logo {
            width: 75px;
            height: auto;
            margin-top: 5px;
            display: block;
        }

        .separador {
            border-top: 1px solid black;
            width: 90%;
            margin: 5px 0;
        }

        .contenedor-texto {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
    </style>
</head>

<body>
    <table>
        @foreach ($bienes->chunk(3) as $fila)
            <tr>
                @foreach ($fila as $bien)
                    <td class="etiqueta">
                        <img src="data:image/svg+xml;base64,{{ $bien->codigo_qr }}" class="qr">

                        <div class="contenedor-texto">
                            <span class="negrita">N° de Inventario:</span>
                            <span class="texto">{{ $bien->numero_inventario }}</span>
                        </div>

                        <div class="contenedor-texto">
                            <span class="negrita">Nombre del bien:</span>
                            <span class="texto">{{ $bien->nombre }}</span>
                        </div>

                        <div class="contenedor-texto">
                            <span class="negrita">Departamento:</span>
                            <span class="texto">{{ $bien->departamento->nombre ?? 'Sin departamento asignado' }}</span>
                        </div>


                        <div class="separador"></div>

                        <img src="data:image/jpeg;base64,{{ $logoBase64 }}" class="logo">
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>
</body>

</html>
