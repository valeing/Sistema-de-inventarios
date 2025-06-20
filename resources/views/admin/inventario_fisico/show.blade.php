@extends('layouts.app')


@section('content')
<div class="container mt-5">
    <h2>Detalles del inventario físico</h2>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route($prefix . '.inventario_fisico.index') }}">Inventario físico</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalles del inventario físico</li>
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
        <h4 class="mb-4">Detalles del inventario físico</h4>

        <table class="table table-borderless">
            <tr>
                <th>Nombre del Inventario</th>
                <td>{{ $inventario->nombre_inventario }}</td>
            </tr>
            <tr>
                <th>Descripción</th>
                <td>{{ $inventario->descripcion }}</td>
            </tr>
            <tr>
                <th>Fecha de Inicio</th>
                <td>{{ $inventario->fecha_inicio }}</td>
            </tr>
            <tr>
                <th>Fecha de Finalización</th>
                <td>{{ $inventario->fecha_finalizacion }}</td>
            </tr>
            <tr>
                <th>Estado</th>
                <td>{{ $inventario->estado }}</td>
            </tr>
            <tr>
                <th>Comentario para Usuarios</th>
                <td>{{ $inventario->comentario }}</td>
            </tr>
        </table>

        <div class="text-center mt-4">
            <a href="{{ route($prefix . '.inventario_fisico.edit', $inventario->id) }}" class="btn btn-primary">Editar</a>
            <a href="{{ route($prefix . '.inventario_fisico.index') }}" class="btn btn-danger">Atrás</a>
        </div>
    </div>
</div>
@endsection
