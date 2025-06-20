@extends('layouts.app')


@section('content')
<div class="container mt-5">
    <h2>Editar inventario físico</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route($prefix . '.inventario_fisico.index') }}">Inventario físico</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar inventario físico</li>
        </ol>
    </nav>

    <!-- Mensaje de éxito -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Mensajes de error -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card p-4">
        <form action="{{ route($prefix . '.inventario_fisico.update', $inventario->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nombre_inventario" class="form-label">Nombre del Inventario</label>
                <input type="text" class="form-control capitalize" id="nombre_inventario" name="nombre_inventario" value="{{ $inventario->nombre_inventario }}" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción del Inventario</label>
                <input type="text" class="form-control capitalize" id="descripcion" name="descripcion" value="{{ $inventario->descripcion }}" required>
            </div>

            <div class="mb-3">
                <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ $inventario->fecha_inicio }}" required>
            </div>

            <div class="mb-3">
                <label for="fecha_finalizacion" class="form-label">Fecha de Finalización</label>
                <input type="date" class="form-control" id="fecha_finalizacion" name="fecha_finalizacion" value="{{ $inventario->fecha_finalizacion }}" required>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label><br>
                <input type="radio" id="programado" name="estado" value="Programado" {{ $inventario->estado == 'Programado' ? 'checked' : '' }}>
                <label for="programado">Programado</label><br>
                <input type="radio" id="completado" name="estado" value="Completado" {{ $inventario->estado == 'Completado' ? 'checked' : '' }}>
                <label for="completado">Completado</label>
            </div>

            <div class="mb-3">
                <label for="comentario" class="form-label">Comentario para Usuarios</label>
                <input type="text" class="form-control capitalize" id="comentario" name="comentario" value="{{ $inventario->comentario }}">
            </div>

            <!-- Centrar botones -->
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary mx-2">Actualizar inventario</button>
                <a href="{{ route($prefix . '.inventario_fisico.index') }}" class="btn btn-danger mx-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.capitalize').forEach(element => {
        element.addEventListener('input', function() {
            // Convierte solo la primera letra de la primera palabra a mayúscula y el resto en minúsculas
            this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();
        });
    });
</script>

@endsection
