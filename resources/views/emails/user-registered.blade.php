<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciales de Acceso</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .header {
            background: #007bff;
            color: #ffffff;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
            text-align: left;
        }
        h1 {
            color: #333333;
            text-align: center;
            font-size: 22px;
            margin-bottom: 20px;
        }
        p {
            color: #555555;
            line-height: 1.6;
            font-size: 16px;
        }
        ul {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        ul li {
            background: #f8f9fa;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            color: #333333;
        }
        .btn-container {
            text-align: center; /* Centra el botón */
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
        }
        .btn:hover {
            background: #0056b3;
            color: #ffffff !important;
        }
        .footer {
            text-align: center;
            color: #888888;
            margin-top: 20px;
            font-size: 14px;
            padding-top: 20px;
            border-top: 1px solid #dddddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Sistema de resguardos de inventarios(SRI)
        </div>
        <div class="content">
            <h1>¡Bienvenido, {{ $name }}!</h1>
            <p>Estas son tus credenciales de acceso al sistema:</p>
            <ul>
                <li><strong>Correo Electrónico:</strong> {{ $email }}</li>
                <li><strong>Contraseña:</strong> {{ $password }}</li>
            </ul>
            <p>Por favor, inicia sesión en el sistema y cambia tu contraseña lo antes posible para garantizar la seguridad de tu cuenta.</p>

            <!-- Contenedor centrado del botón -->
            <div class="btn-container">
                <a href="{{ url('/login') }}" class="btn">Iniciar Sesión</a>
            </div>
        </div>
        <div class="footer">
            <p>Si tienes algún problema, no dudes en contactar al administrador del sistema.</p>
        </div>
    </div>
</body>
</html>
