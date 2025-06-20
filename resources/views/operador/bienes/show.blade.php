@extends('layouts.app')

@section('title', 'Detalles del bien')

@section('content')
    <div class="container mt-5">
        <h2>Detalles del bien</h2>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route($prefix . '.bienes.index') }}">Bienes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalles</li>
            </ol>
        </nav>

        <div class="card p-4">
            <div class="row">
                <!-- Columna principal con los detalles del bien -->
                <div class="col-lg-8 col-md-12">
                    <div class="card p-4 mb-4">
                        <h4 class="mb-4">Información del bien</h4>
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th>N° de inventario:</th>
                                    <td>{{ $bien->numero_inventario }}</td>
                                </tr>
                                <tr>
                                    <th>N° de serie:</th>
                                    <td>{{ $bien->numero_serie }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre:</th>
                                    <td>{{ $bien->nombre }}</td>
                                </tr>
                                <tr>
                                    <th>Estado del bien:</th>
                                    <td>{{ ucfirst($bien->estado) }}</td>
                                </tr>
                                <tr>
                                    <th>Descripción general:</th>
                                    <td>{{ $bien->descripcion_general }}</td>
                                </tr>
                                <tr>
                                    <th>Observaciones:</th>
                                    <td>{{ $bien->observaciones }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha de adquisición del bien:</th>
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
                                <tr>
                                    <th>Resguardante:</th>
                                    <td>
                                        @if ($bien->asignacion)
                                            {{ $bien->asignacion->resguardante->nombre_apellido }}
                                        @else
                                            No asignado
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Costo:</th>
                                    <td><strong>${{ number_format($bien->costo, 2) }} MXN</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Columna lateral con imagen y QR -->
                <div class="col-lg-4 col-md-12 d-flex flex-column align-items-center">
                    <!-- Imagen del bien -->
                    <div class="card mb-4 p-3 text-center" style="width: 100%;">
                        <h5>Imagen</h5>
                        <div class="d-flex justify-content-center">
                            @if ($bien->imagen)
                                <img src="data:{{ $bien->mime_type }};base64,{{ $bien->imagen }}" alt="Imagen del bien"
                                    class="img-fluid rounded" style="max-width: 200px; height: auto; margin: 0 auto;">
                            @else
                                <img src="https://via.placeholder.com/200" alt="Imagen no disponible"
                                    class="img-fluid rounded" style="max-width: 200px; height: auto; margin: 0 auto;">
                            @endif
                        </div>
                    </div>

                    <!-- QR del bien -->
                    <div class="card mb-4 p-3 text-center" style="width: 100%;">
                        <h5>QR</h5>
                        <div>
                            @if ($bien->codigo_qr)
                                {!! base64_decode($bien->codigo_qr) !!}
                            @else
                                <p>Código QR no disponible</p>
                            @endif
                        </div>
                        <a href="{{ route($prefix . '.bienes.pdf', $bien->id_bien) }}" class="btn btn-success mt-2">
                            <i class="fas fa-file-pdf"></i> Descargar PDF
                        </a>

                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route($prefix . '.bienes.edit', $bien->id_bien) }}"
                    class="btn btn-primary mx-2 px-4">Editar</a>
                <a href="{{ route($prefix . '.bienes.index') }}" class="btn btn-danger mx-2 px-4">Atrás</a>
            </div>

        </div>

    </div>
@endsection
