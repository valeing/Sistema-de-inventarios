<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Inventarios')</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }
        .navbar-brand {
            color: white !important;
            font-weight: bold;
            white-space: nowrap; /* Evita que el texto de la navbar se rompa en varias líneas */
        }
        .container {
            flex: 1; /* Permite que el contenido se expanda */
            display: flex;
            flex-direction: column;
            align-items: center; /* Centra el contenido horizontalmente */
        }
        .content-wrapper {
            width: 100%;
            max-width: 900px; /* Establece un ancho máximo para evitar que el contenido sea demasiado ancho */
            text-align: center;
            overflow-wrap: break-word; /* Evita el desbordamiento del texto */
            word-wrap: break-word;
        }
        .footer {
            background: #343a40;
            color: white;
            padding: 15px 0;
            text-align: center;
            width: 100%;
            margin-top: auto; /* Empuja el footer al fondo */
        }
        .footer a {
            color: #ffc107;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-boxes"></i> Sistemas de Resguardo de Inventarios
            </a>
        </div>
    </nav>

    <!-- Contenido -->
    <div class="container mt-4">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p class="mb-1">© {{ date('Y') }} Universidad Politécnica de Bacalar</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
