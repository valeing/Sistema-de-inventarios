@extends('layouts.app')


@section('title', 'Detalles del Usuario')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Detalles del Usuario</h2>
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.users.index") }}">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalles</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header bg-primary text-white">
                Información del Usuario
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <th>Nombre Completo</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Correo Electrónico</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Rol</th>
                        <td>
                            <span
                                class="badge
                            @switch($user->role->name)
                                @case('Administrador') bg-danger @break
                                @case('Operador') bg-warning text-dark @break
                                @default bg-success
                            @endswitch">
                                {{ $user->role->name }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Fecha de Registro</th>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Si el usuario tiene un resguardante vinculado, mostrar sus datos -->
        @if ($user->resguardante)
            <div class="card mt-4">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h5 class="mb-0">Resguardante Asignado</h5>
                    <button id="desvincular-resguardante" class="btn btn-danger" data-user-id="{{ $user->id }}"
                        data-url="{{ route("$prefix.users.desvincular", $user->id) }}">
                        <i class="fas fa-unlink"></i> Desvincular
                    </button>


                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>ID Resguardante</th>
                            <td>{{ $user->resguardante->id_resguardante }}</td>
                        </tr>
                        <tr>
                            <th>Nombre</th>
                            <td>{{ $user->resguardante->nombre_apellido }}</td>
                        </tr>
                        <tr>
                            <th>Número de Empleado</th>
                            <td>{{ $user->resguardante->numero_empleado }}</td>
                        </tr>
                        <tr>
                            <th>Departamento</th>
                            <td>{{ $user->resguardante->departamento->nombre ?? 'No asignado' }}</td>
                        </tr>
                        <tr>
                            <th>Teléfono</th>
                            <td>{{ $user->resguardante->telefono ?? 'No disponible' }}</td>
                        </tr>
                        <tr>
                            <th>Estado</th>
                            <td>
                                <span
                                    class="badge {{ $user->resguardante->estado === 'activo' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($user->resguardante->estado) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        <a href="{{ route("$prefix.users.index") }}" class="btn btn-primary mt-3">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- CSRF Token para AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script para manejar la desvinculación del resguardante -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const botonDesvincular = document.getElementById('desvincular-resguardante');

            if (botonDesvincular) {
                botonDesvincular.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const url = this.getAttribute('data-url'); // URL obtenida del botón

                    if (!url) {
                        Swal.fire("Error", "No se encontró la URL de desvinculación.", "error");
                        return;
                    }

                    Swal.fire({
                        title: "¿Estás seguro?",
                        text: "Esta acción desvinculará al resguardante del usuario.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Sí, desvincular",
                        cancelButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(url, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content'),
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: "Desvinculado",
                                            text: data.message,
                                            icon: "success",
                                            confirmButtonText: "OK"
                                        }).then(() => {
                                            // Recargar la página después de la desvinculación
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire("Error", data.message ||
                                            "No se pudo completar la solicitud.", "error");
                                    }
                                })
                                .catch(error => {
                                    console.error('Error al desvincular:', error);
                                    Swal.fire("Error",
                                        "Ocurrió un problema inesperado. Inténtalo de nuevo.",
                                        "error");
                                });
                        }
                    });
                });
            }
        });
    </script>


@endsection
