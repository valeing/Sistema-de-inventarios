@extends('layouts.app')


@section('content')
    <div class="container mt-5">
        <h2 class="text-center text-primary fw-bold">Historial de Baja del Bien</h2>

        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-light p-3 rounded">
                <li class="breadcrumb-item">
                    <a href="{{ route("$prefix.dashboard") }}" class="text-decoration-none">Inicio</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route("$prefix.bajas.index") }}" class="text-decoration-none">Historial de bajas</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detalles de la baja</li>
            </ol>
        </nav>


        <!-- Mensajes -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> Se encontraron los siguientes errores:
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm p-4">
            <h3 class="text-center text-primary mb-4"><i class="bi bi-archive"></i> Historial de Baja del Bien</h3>

            <div class="row g-4">
                <!-- Detalles de la Baja -->
                <div class="col-md-6">
                    <div class="card shadow-sm rounded p-4 h-100">
                        <h5 class="text-primary mb-3"><i class="bi bi-clipboard-check"></i> Detalles de la Baja</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><strong><i class="bi bi-box-seam me-2"></i>N.º de Inventario:</strong>
                                {{ $baja->numero_inventario }}</li>
                            <li class="mb-2"><strong><i class="bi bi-calendar-event me-2"></i>Fecha de Baja:</strong>
                                {{ $baja->fecha_baja }}</li>
                            <li class="mb-2"><strong><i class="bi bi-exclamation-triangle me-2"></i>Motivo de
                                    Baja:</strong> {{ $baja->motivo }}</li>
                            <li class="mb-2"><strong><i class="bi bi-info-circle me-2"></i>Descripción del
                                    Problema:</strong> {{ $baja->descripcion_problema ?? 'N/A' }}</li>
                            <li class="mb-2"><strong><i class="bi bi-chat-left-text me-2"></i>Observaciones:</strong>
                                {{ $baja->observaciones ?? 'N/A' }}</li>
                        </ul>
                        <a href="{{ route("$prefix.bajas.download", $baja->id) }}" class="btn btn-primary mt-3 w-100">
                            <i class="bi bi-download"></i> Descargar Expediente
                        </a>
                    </div>
                </div>

                <!-- Detalles del Bien -->
                <div class="col-md-6">
                    <div class="card shadow-sm rounded p-4 h-100">
                        <h5 class="text-success mb-3"><i class="bi bi-box"></i> Detalles del Bien</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><strong><i class="bi bi-tag me-2"></i>Nombre:</strong> {{ $baja->nombre }}
                            </li>
                            <li class="mb-2">
                                <strong><i class="bi bi-shield-check me-2"></i>Estado:</strong>
                                <span class="badge bg-secondary">{{ ucfirst($baja->estado) }}</span>
                            </li>
                            <li class="mb-2"><strong><i class="bi bi-building me-2"></i>Departamento:</strong>
                                {{ $baja->departamento }}</li>
                            <li class="mb-2"><strong><i class="bi bi-grid me-2"></i>Categoría:</strong>
                                {{ $baja->categoria }}</li>
                            <li class="mb-2"><strong><i class="bi bi-calendar3 me-2"></i>Fecha de Adquisición:</strong>
                                {{ $baja->fecha_adquisicion }}</li>
                            <li class="mb-2"><strong><i class="bi bi-upc-scan me-2"></i>N.º de Serie:</strong>
                                {{ $baja->numero_serie }}</li>
                            <li class="mb-2"><strong><i class="bi bi-person me-2"></i>Resguardante:</strong>
                                {{ $baja->nombre_apellido ?? 'N/A' }}</li>

                            @if ($baja->imagen)
                                <li class="mt-3 text-center">
                                    <img src="data:image/jpeg;base64,{{ $baja->imagen }}" alt="Imagen del Bien"
                                        class="img-thumbnail rounded shadow-sm" style="max-width: 220px;">
                                </li>
                            @else
                                <li class="text-muted mt-3 text-center"><i class="bi bi-image me-2"></i>No hay imagen
                                    disponible.</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="text-center mt-4">
                <a href="{{ route("$prefix.bajas.export", $baja->id) }}" class="btn btn-purple mx-2 px-4"
                    style="background-color: #6f42c1; color: white;">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Exportar PDF
                </a>
                <a href="{{ route("$prefix.bajas.index") }}" class="btn btn-danger mx-2 px-4">
                    <i class="bi bi-arrow-left"></i> Atrás
                </a>
            </div>
        </div>

    </div>
@endsection
