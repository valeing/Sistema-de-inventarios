@extends('layouts.app')


@section('title', 'Bienes asignados')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Bienes asignados a {{ $resguardante->nombre_apellido }}</h2>
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.asignaciones.index") }}">Asignaciones</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bienes asignados</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route("$prefix.asignaciones.index") }}" class="btn btn-success">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="card p-4">
            <h4 class="mb-4">Listado de Bienes</h4>

            @if ($bienes->isEmpty())
                <div class="alert alert-info text-center">No hay bienes asignados a este resguardante.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th style="width: 15%;">N° de Inventario</th>
                                <th style="width: 30%;">Nombre del Bien</th>
                                <th style="width: 20%;">Fecha de Asignación</th>
                                <th style="width: 10%;">Imagen</th> <!-- Nueva columna para la imagen -->
                                <th style="width: 15%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bienes as $bien)
                                <tr class="table-light" id="row-{{ $bien->id_bien }}">
                                    <td class="text-center fw-bold">{{ $bien->numero_inventario }}</td>
                                    <td>{{ $bien->nombre }}</td>
                                    <td class="text-center">
                                        {{ optional($bien->asignacion)->fecha_asignacion ?? 'No registrada' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($bien->imagen)
                                            <img src="data:{{ $bien->mime_type }};base64,{{ $bien->imagen }}"
                                                alt="Imagen del bien" class="img-thumbnail rounded"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <img src="https://via.placeholder.com/50" alt="Imagen no disponible"
                                                class="img-thumbnail rounded"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex justify-content-center gap-3">
                                            <!-- Botón Editar bien -->
                                            <a href="{{ route("$prefix.asignaciones.edit", $bien->id_bien) }}"
                                                class="text-info" title="Editar bien">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Botón Eliminar bien -->
                                            <button class="btn p-0 text-danger delete-bien-btn"
                                                data-id="{{ $bien->id_bien }}"
                                                data-url="{{ route("$prefix.asignaciones.desasignarIndividual", $bien->id_bien) }}"
                                                title="Eliminar bien">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                <!-- Paginación personalizada -->
                <div class="d-flex justify-content-center my-3">
                    {{ $bienes->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- CSRF Token para solicitudes AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.delete-bien-btn').forEach(button => {
                button.addEventListener('click', function() {
                    let bienId = this.getAttribute('data-id');
                    let url = this.getAttribute('data-url');

                    Swal.fire({
                        title: "¿Estás seguro?",
                        text: "Este bien será eliminado de la asignación.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Sí, eliminar",
                        cancelButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(url, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content'),
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.error) {
                                        Swal.fire("Error", data.error, "error");
                                    } else {
                                        Swal.fire({
                                            title: "Eliminado",
                                            text: "El bien ha sido eliminado correctamente.",
                                            icon: "success",
                                        }).then(() => {
                                            location
                                        .reload(); // Recarga la página después de aceptar el mensaje
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error("Error:", error);
                                    Swal.fire("Error",
                                        "Hubo un error al eliminar el bien.",
                                        "error");
                                });
                        }
                    });
                });
            });

            @if (session('success'))
                Swal.fire({
                    title: "Éxito",
                    text: "{{ session('success') }}",
                    icon: "success"
                }).then(() => {
                    location.reload(); // Recarga la página después de aceptar el mensaje de éxito
                });
            @endif

            @if (session('error'))
                Swal.fire("Error", "{{ session('error') }}", "error");
            @endif
        });
    </script>

@endsection
