@extends('layouts.resguardante')

@section('title', 'Detalles del Bien')

@section('content')
    <div class="container mt-4">
        <h2>Detalles del Bien</h2>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('resguardante.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('resguardante.mis-bienes') }}">Mis Bienes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalles</li>
            </ol>
        </nav>

        <div class="card p-4">
            <div class="row">
                <!-- Columna principal con los detalles del bien -->
                <div class="col-lg-8 col-md-12 col-12 mb-3">
                    <div class="card p-3">
                        <h4 class="mb-3 text-center">Información del Bien</h4>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th>N° de Inventario:</th>
                                        <td class="text-break">{{ $bien->numero_inventario }}</td>
                                    </tr>
                                    <tr>
                                        <th>N° de Serie:</th>
                                        <td class="text-break">{{ $bien->numero_serie }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nombre:</th>
                                        <td>{{ $bien->nombre }}</td>
                                    </tr>
                                    <tr>
                                        <th>Estado del Bien:</th>
                                        <td>
                                            <span class="badge
                                            @switch($bien->estado)
                                                @case('activo') bg-success @break
                                                @case('mantenimiento') bg-warning text-dark @break
                                                @default bg-danger
                                            @endswitch">
                                                {{ ucfirst($bien->estado) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Descripción General:</th>
                                        <td>{{ $bien->descripcion_general }}</td>
                                    </tr>
                                    <tr>
                                        <th>Observaciones:</th>
                                        <td>{{ $bien->observaciones ?? 'Sin observaciones' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Fecha de Adquisición:</th>
                                        <td>{{ \Carbon\Carbon::parse($bien->fecha_adquisicion)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Departamento:</th>
                                        <td>{{ $bien->departamento->nombre ?? 'No asignado' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Categoría:</th>
                                        <td>{{ $bien->categoria }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Columna lateral con imagen y QR -->
                <div class="col-lg-4 col-md-12 col-12 d-flex flex-column align-items-center">
                    <!-- Imagen del bien -->
                    <div class="card mb-3 p-3 text-center w-100">
                        <h5>Imagen</h5>
                        <div class="d-flex justify-content-center">
                            @if ($bien->imagen)
                                <img src="data:{{ $bien->mime_type }};base64,{{ $bien->imagen }}"
                                    alt="Imagen del bien" class="img-fluid rounded w-100" style="max-width: 200px;">
                            @else
                                <img src="https://via.placeholder.com/200" alt="Imagen no disponible"
                                    class="img-fluid rounded w-100" style="max-width: 200px;">
                            @endif
                        </div>
                    </div>

                    <!-- QR del bien -->
                    <div class="card mb-3 p-3 text-center w-100">
                        <h5>QR</h5>
                        <div>
                            @if ($bien->codigo_qr)
                                {!! base64_decode($bien->codigo_qr) !!}
                            @else
                                <p>Código QR no disponible</p>
                            @endif
                        </div>
                        <a href="{{ route('bienes.pdf', $bien->id_bien) }}" class="btn btn-success mt-2 w-100">
                            <i class="fas fa-file-pdf"></i> Descargar PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('resguardante.mis-bienes') }}" class="btn btn-danger px-4">Atrás</a>
            </div>
        </div>
    </div>
@endsection
