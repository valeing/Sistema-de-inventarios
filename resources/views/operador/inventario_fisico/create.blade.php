@extends('layouts.app')



@section('content')
<div class="container mt-5">
    <h2>Programar Inventario Físico</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route($prefix . '.inventario_fisico.index') }}">Inventario físico</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear inventario físico</li>
        </ol>
    </nav>

    <div class="card p-4">
        <form id="inventarioForm" action="{{ route($prefix . '.inventario_fisico.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nombre_inventario" class="form-label">Nombre del Inventario</label>
                <input type="text" class="form-control capitalize" id="nombre_inventario" name="nombre_inventario" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción del Inventario</label>
                <input type="text" class="form-control capitalize" id="descripcion" name="descripcion" required>
            </div>

            <div class="mb-3">
                <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
            </div>

            <div class="mb-3">
                <label for="fecha_finalizacion" class="form-label">Fecha de Finalización</label>
                <input type="date" class="form-control" id="fecha_finalizacion" name="fecha_finalizacion" required>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label><br>
                <input type="radio" id="programado" name="estado" value="Programado" checked>
                <label for="programado">Programado</label><br>
                <input type="radio" id="completado" name="estado" value="Completado">
                <label for="completado">Completado</label>
            </div>

            <div class="mb-3">
                <label for="comentario" class="form-label">Comentario para Usuarios</label>
                <input type="text" class="form-control capitalize" id="comentario" name="comentario">
            </div>

            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-success mx-2" onclick="confirmarRegistro()">Programar inventario</button>
                <a href="{{ route($prefix . '.inventario_fisico.index') }}" class="btn btn-danger mx-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.capitalize').forEach(element => {
        element.addEventListener('input', function() {
            this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();
        });
    });

    function confirmarRegistro() {
        Swal.fire({
            title: "¿Confirmar Programación?",
            text: "¿Estás seguro de que deseas programar este inventario físico?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, programar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('inventarioForm').submit();
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        let successMessage = @json(session('success'));
        let errorMessage = @json(session('error'));

        if (successMessage) {
            Swal.fire({
                icon: "success",
                title: "¡Éxito!",
                text: successMessage,
                confirmButtonColor: "#28a745",
            });
        }

        if (errorMessage) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: errorMessage,
                confirmButtonColor: "#d33",
            });
        }
    });
</script>

@endsection
