@extends('layouts.app')


@section('title', 'Baja del bien')

@section('content')
    <div class="container mt-5">
        <h2>Baja del bien</h2>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.bajas.index") }}">Historial de bajas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Formulario de bajas</li>
            </ol>
        </nav>

        <div class="card p-4">
            <form id="baja-form" action="{{ route("$prefix.bajas.store") }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Campo para seleccionar bien -->
                <div class="mb-3">
                    <label for="id_bien" class="form-label">N.º de Inventario del Bien</label>
                    <div class="input-group">
                        <input type="text" id="bienSeleccionado" class="form-control" placeholder="Seleccione un bien..."
                            readonly>
                        <input type="hidden" id="id_bien" name="id_bien">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalSeleccionarBien">
                            <i class="bi bi-search"></i> Seleccionar
                        </button>
                    </div>
                </div>

                <!-- Incluir el modal -->
                @include('componentes.modal_seleccionar_bien')


                <!-- Motivo de la Baja -->
                <div class="mb-3">
                    <label for="motivo" class="form-label">Motivo de la baja</label>
                    <select class="form-select" id="motivo" name="motivo" required>
                        <option value="">Seleccione el motivo de la baja</option>
                        <option value="Defectuoso">Defectuoso</option>
                        <option value="Pérdida parcial">Pérdida parcial</option>
                        <option value="No funcional">No funcional</option>
                    </select>
                </div>

                <!-- Descripción del Problema -->
                <div class="mb-3">
                    <label for="descripcion_problema" class="form-label">Descripción del problema</label>
                    <input type="text" class="form-control" id="descripcion_problema" name="descripcion_problema"
                        required>
                </div>

                <!-- Fecha de la Baja -->
                <div class="mb-3">
                    <label for="fecha_baja" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="fecha_baja" name="fecha_baja" required>
                </div>

                <!-- Expediente de Baja -->
                <div class="mb-3">
                    <label for="expediente" class="form-label">Agregar expediente de baja (PDF, máx. 10MB)</label>
                    <input type="file" class="form-control" id="expediente" name="expediente" required>
                </div>

                <!-- Botones de Acción -->
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-success mx-2" onclick="confirmarBaja()">Confirmar baja</button>
                    <a href="{{ route("$prefix.bajas.index") }}" class="btn btn-danger mx-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let successMessage = @json(session('success'));
            let errorMessages = @json($errors->all());

            if (successMessage) {
                Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: successMessage,
                    confirmButtonColor: "#28a745",
                });
            }

            if (errorMessages.length > 0) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    html: errorMessages.join("<br>"),
                    confirmButtonColor: "#d33",
                });
            }
        });

        function confirmarBaja() {
            let expedienteInput = document.getElementById("expediente");

            // Validar si se ha seleccionado un archivo
            if (expedienteInput.files.length === 0) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Debe seleccionar un archivo antes de confirmar la baja.",
                    confirmButtonColor: "#d33",
                });
                return;
            }

            let file = expedienteInput.files[0];
            let fileSizeMB = file.size / 1024 / 1024; // Convertir bytes a MB
            let fileType = file.type;

            // Validar tipo de archivo (debe ser PDF)
            if (fileType !== "application/pdf") {
                Swal.fire({
                    icon: "error",
                    title: "Formato de archivo no válido",
                    text: "Solo se admiten archivos en formato PDF.",
                    confirmButtonColor: "#d33",
                });
                return;
            }

            // Validar tamaño del archivo (máximo 10MB)
            if (fileSizeMB > 10) {
                Swal.fire({
                    icon: "error",
                    title: "Archivo demasiado grande",
                    text: "El archivo no debe superar los 10MB.",
                    confirmButtonColor: "#d33",
                });
                return;
            }

            Swal.fire({
                title: "¿Confirmar baja del bien?",
                text: "Esta acción no se puede deshacer.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, confirmar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("baja-form").submit();
                }
            });
        }

    </script>

@endsection
