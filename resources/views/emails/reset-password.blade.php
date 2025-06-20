<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
</head>
<body style="background-color: #f4f4f9; margin: 0; padding: 0; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 20px auto; background: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1);">

        <!-- Encabezado -->
        <div style="background: #007bff; color: #ffffff; text-align: center; padding: 15px; border-radius: 10px 10px 0 0;">
            <h2 style="margin: 0;">Sistema de Resguardos de Inventarios(SRI)</h2>
        </div>

        <!-- Cuerpo del correo -->
        <div style="padding: 20px; text-align: center;">
            <h3 style="color: #333;">Recuperación de Contraseña</h3>
            <p style="color: #555;">Hemos recibido una solicitud para restablecer tu contraseña.</p>
            <p style="color: #555;">Haz clic en el siguiente botón para continuar:</p>

            <!-- Botón de restablecimiento de contraseña -->
            <a href="{{ url('password/reset', $token) . '?email=' . urlencode($email) }}"
                style="display: inline-block; background: #007bff; color: #ffffff; padding: 12px 20px; text-decoration: none;
                font-size: 16px; font-weight: bold; border-radius: 5px; margin-top: 15px;">
                Restablecer Contraseña
            </a>

            <p style="margin-top: 20px; color: #555;">Este enlace expirará en <strong>60 minutos</strong>.</p>
            <p style="color: #555;">Si no solicitaste un restablecimiento de contraseña, ignora este correo.</p>
        </div>

        <!-- Pie de página -->
        <div style="background: #f4f4f9; text-align: center; padding: 10px; border-radius: 0 0 10px 10px; font-size: 14px; color: #888;">
            <p>© {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
