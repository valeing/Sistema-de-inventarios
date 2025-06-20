@extends('layouts.public-layout')

@section('title', 'Detalles del Bien')

@section('content')
<div class="container mt-5">
    <h2 class="text-center fw-bold text-primary">
        <i class="fas fa-info-circle"></i> Detalles del Bien
    </h2>

    <div class="card shadow-lg p-4 mt-4">
        <div class="row">
            <!-- Sección de Información -->
            <div class="col-lg-8 col-md-12">
                <h4 class="text-primary fw-bold">
                    <i class="fas fa-box-open"></i> Información del Bien
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="bg-light"><i class="fas fa-hashtag"></i> N° de Inventario:</th>
                                <td class="text-break">{{ $bien->numero_inventario }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light"><i class="fas fa-barcode"></i> N° de Serie:</th>
                                <td class="text-break">{{ $bien->numero_serie ?? 'No disponible' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light"><i class="fas fa-tag"></i> Nombre:</th>
                                <td>{{ $bien->nombre }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light"><i class="fas fa-check-circle"></i> Estado:</th>
                                <td>
                                    <span class="badge {{ $bien->estado == 'activo' ? 'bg-success' : ($bien->estado == 'mantenimiento' ? 'bg-warning' : 'bg-danger') }}">
                                        {{ ucfirst($bien->estado) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light"><i class="fas fa-align-left"></i> Descripción:</th>
                                <td class="text-break">{{ $bien->descripcion_general }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light"><i class="fas fa-exclamation-circle"></i> Observaciones:</th>
                                <td class="text-break">{{ $bien->observaciones ?? 'Sin observaciones' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light"><i class="fas fa-calendar-alt"></i> Fecha de Adquisición:</th>
                                <td>{{ \Carbon\Carbon::parse($bien->fecha_adquisicion)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light"><i class="fas fa-building"></i> Departamento:</th>
                                <td>{{ $bien->departamento->nombre ?? 'No asignado' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light"><i class="fas fa-folder"></i> Categoría:</th>
                                <td>{{ $bien->categoria }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light"><i class="fas fa-user"></i> Resguardante:</th>
                                <td>
                                    @if($bien->asignacion)
                                        {{ $bien->asignacion->resguardante->nombre_apellido }}
                                    @else
                                        <span class="text-muted">No asignado</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sección de Imagen y Código QR -->
            <div class="col-lg-4 col-md-12">
                <div class="card p-3 shadow-sm mb-4 text-center">
                    <h5 class="text-secondary"><i class="fas fa-image"></i> Imagen del Bien</h5>
                    <div class="d-flex justify-content-center">
                        @if($bien->imagen)
                            <img src="data:{{ $bien->mime_type }};base64,{{ $bien->imagen }}"
                                 alt="Imagen del bien" class="img-fluid rounded shadow" style="max-width: 100%;">
                        @else
                            <img src="https://via.placeholder.com/300"
                                 alt="Imagen no disponible" class="img-fluid rounded shadow">
                        @endif
                    </div>
                </div>

                <div class="card p-3 shadow-sm text-center">
                    <h5 class="text-secondary"><i class="fas fa-qrcode"></i> Código QR</h5>
                    @if($bien->codigo_qr)
                        <div class="d-flex justify-content-center">
                            {!! base64_decode($bien->codigo_qr) !!}
                        </div>
                    @else
                        <p class="text-muted">Código QR no disponible</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-responsive td {
        word-wrap: break-word;
        overflow-wrap: break-word;
        max-width: 250px;
    }
    .card {
        border-radius: 10px;
    }
    .badge {
        font-size: 14px;
        padding: 8px 12px;
    }
</style>
@endsection
