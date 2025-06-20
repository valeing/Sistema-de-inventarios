@extends('layouts.resguardante')

@section('title', 'Dashboard')

@section('content')
    <div class="container mt-4">
        <h1 class="h3 mb-4 text-gray-800">
            Bienvenido al panel del
            {{ Auth::user()->role->name === 'Administrador' ? 'Administración' : (Auth::user()->role->name === 'Operador' ? 'Operador' : 'Resguardante') }}
        </h1>

        <!-- Mensaje de Bienvenida -->
        <div class="alert alert-primary">
            <h4>Bienvenido, {{ auth()->user()->name }} 👋</h4>
            <p>Hoy es {{ date('d/m/Y') }}. ¡Esperamos que tengas un excelente día!</p>
        </div>

        <!-- 📌 Aviso de Inventario Físico (Cargado dinámicamente) -->
        <div id="aviso-inventario"></div>

        <div class="row">
        </div>
    </div>

    <script>
        function actualizarAviso() {
            fetch('/api/inventario/ultimo')
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        // Si no hay avisos, mostrar un mensaje predeterminado
                        document.getElementById('aviso-inventario').innerHTML = `
                        <div style="background-color: #e9ecef; border-left: 6px solid #6c757d; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                            <h4 style="color: #495057; font-weight: bold; margin-bottom: 5px;">Avisos del Sistema</h4>
                            <p style="color: #6c757d;">${data.message}</p>
                        </div>
                    `;
                    } else {
                        // Si hay un inventario programado, mostrar la información
                        document.getElementById('aviso-inventario').innerHTML = `
                        <div style="background-color: #f8d7da; border-left: 6px solid #dc3545; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                            <h4 style="color: #721c24; font-weight: bold; margin-bottom: 5px;">Aviso de Inventario</h4>
                            <p><strong>Nombre del Inventario:</strong> ${data.nombre}</p>
                            <p><strong>Fecha de Inicio:</strong> ${data.fecha_inicio}</p>
                            <p><strong>Fecha de Finalización:</strong> ${data.fecha_fin}</p>
                            <p><strong>Descripción:</strong> ${data.descripcion}</p>
                            <p><strong>Comentario:</strong> ${data.comentario}</p>
                        </div>
                    `;
                    }
                })
                .catch(error => {
                    console.warn(error);
                    document.getElementById('aviso-inventario').innerHTML = `
                    <div style="background-color: #fff3cd; border-left: 6px solid #ffc107; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                        <h4 style="color: #856404; font-weight: bold; margin-bottom: 5px;">Error</h4>
                        <p>No se pudo cargar la información de los avisos.</p>
                    </div>
                `;
                });
        }

        // Actualiza el aviso cada 60 segundos (1 minuto)
        setInterval(actualizarAviso, 60000);
        actualizarAviso(); // Ejecuta la función al cargar la página
    </script>

@endsection
