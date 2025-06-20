@extends('layouts.app')

@section('title', 'Ver detalles del resguardante')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4 text-primary">Detalles del Resguardante</h1>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.resguardantes.index") }}">Resguardantes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalles</li>
            </ol>
        </nav>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link {{ request('tab') != 'bienes' ? 'active' : '' }}"
                    href="{{ route("$prefix.resguardantes.show", ['id' => $resguardante->id_resguardante]) }}">
                    Resguardante
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'bienes' ? 'active' : '' }}"
                    href="{{ route("$prefix.resguardantes.show", ['id' => $resguardante->id_resguardante, 'tab' => 'bienes']) }}">
                    Bienes
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Resguardante Tab -->
            <div class="tab-pane fade {{ request('tab') != 'bienes' ? 'show active' : '' }}" id="resguardante">
                <div class="card shadow p-4">
                    <h4 class="mb-3 text-info">Información del Resguardante</h4>
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th class="text-uppercase">Nombre:</th>
                                <td>{{ $resguardante->nombre_apellido }}</td>
                            </tr>
                            <tr>
                                <th class="text-uppercase">Dirección:</th>
                                <td>{{ $resguardante->direccion->nombre }}</td>
                            </tr>
                            <tr>
                                <th class="text-uppercase">Departamento:</th>
                                <td>{{ $resguardante->departamento->nombre }}</td>
                            </tr>
                            <tr>
                                <th class="text-uppercase">Fecha de Alta:</th>
                                <td>{{ \Carbon\Carbon::parse($resguardante->fecha)->format('d/m/Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bienes Tab -->
            <div class="tab-pane fade {{ request('tab') == 'bienes' ? 'show active' : '' }}" id="bienes">
                <div class="card shadow p-4">
                    <h4 class="mb-3 text-info">Bienes Asignados</h4>

                    @if ($bienes_asignados->isEmpty())
                        <div class="alert alert-warning text-center">Este resguardante no tiene bienes asignados.</div>
                    @else
                        <form id="bienesForm" action="{{ route("$prefix.bienes.exportar.bienes") }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="bg-primary text-white text-center">
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>
                                            <th class="text-uppercase">N° Inventario</th>
                                            <th class="text-uppercase">Imagen</th>
                                            <th class="text-uppercase">Nombre del Bien</th>
                                            <th class="text-uppercase">Categoría</th>
                                            <th class="text-uppercase">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bienes_asignados as $asignacion)
                                            <tr class="table-light">
                                                <td class="text-center">
                                                    <input type="checkbox" name="bienes[]"
                                                        value="{{ $asignacion->bien->id_bien }}">
                                                </td>
                                                <td class="text-center">{{ $asignacion->bien->numero_inventario }}</td>
                                                <td class="text-center">
                                                    @if ($asignacion->bien->imagen)
                                                        <img src="data:image/png;base64,{{ $asignacion->bien->imagen }}"
                                                            alt="Imagen del bien" width="50">
                                                    @else
                                                        <img src="https://via.placeholder.com/50" alt="Imagen no disponible"
                                                            width="50">
                                                    @endif
                                                </td>
                                                <td>{{ $asignacion->bien->nombre }}</td>
                                                <td>{{ $asignacion->bien->categoria }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-3">
                                                        <button type="button" class="btn btn-link text-info p-0"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDetallesBien{{ $asignacion->bien->id_bien }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>


                                                        <a href="{{ route("$prefix.bienes.edit", $asignacion->bien->id_bien) }}"
                                                            class="text-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                @foreach ($bienes_asignados as $asignacion)
                                    <div class="modal fade" id="modalDetallesBien{{ $asignacion->bien->id_bien }}"
                                        tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header py-2">
                                                    <h5 class="modal-title">Detalles del Bien</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body px-4 py-3">
                                                    <div class="row">
                                                        <!-- Columna izquierda: datos -->
                                                        <div class="col-md-8">
                                                            <table class="table table-sm table-borderless mb-0">
                                                                <tbody>
                                                                    <tr>
                                                                        <th class="w-50">N° de inventario:</th>
                                                                        <td>{{ $asignacion->bien->numero_inventario }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>N° de serie:</th>
                                                                        <td>{{ $asignacion->bien->numero_serie }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Nombre:</th>
                                                                        <td>{{ $asignacion->bien->nombre }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Estado del bien:</th>
                                                                        <td>{{ ucfirst($asignacion->bien->estado) }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Descripción general:</th>
                                                                        <td>{{ $asignacion->bien->descripcion_general }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Observaciones:</th>
                                                                        <td>{{ $asignacion->bien->observaciones }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Fecha de adquisición:</th>
                                                                        <td>{{ \Carbon\Carbon::parse($asignacion->bien->fecha_adquisicion)->format('d/m/Y') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Departamento:</th>
                                                                        <td>{{ $asignacion->bien->departamento->nombre ?? 'No asignado' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Categoría:</th>
                                                                        <td>{{ $asignacion->bien->categoria }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Resguardante:</th>
                                                                        <td>
                                                                            {{ $asignacion->resguardante->nombre_apellido ?? 'No asignado' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Costo:</th>
                                                                        <td><strong>${{ number_format($asignacion->bien->costo, 2) }}
                                                                                MXN</strong></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <!-- Columna derecha: imagen y QR -->
                                                        <div class="col-md-4 d-flex flex-column align-items-center">
                                                            <div class="mb-3 w-100 text-center">
                                                                <h6 class="mb-2">Imagen</h6>
                                                                @if ($asignacion->bien->imagen)
                                                                    <img src="data:{{ $asignacion->bien->mime_type }};base64,{{ $asignacion->bien->imagen }}"
                                                                        alt="Imagen del bien" class="img-fluid rounded"
                                                                        style="max-height: 150px;">
                                                                @else
                                                                    <img src="https://via.placeholder.com/150"
                                                                        alt="Imagen no disponible"
                                                                        class="img-fluid rounded">
                                                                @endif
                                                            </div>

                                                            <div class="text-center w-100">
                                                                <h6 class="mb-2">Código QR</h6>
                                                                @if ($asignacion->bien->codigo_qr)
                                                                    {!! base64_decode($asignacion->bien->codigo_qr) !!}
                                                                @else
                                                                    <p>Código QR no disponible</p>
                                                                @endif

                                                                <a href="{{ route($prefix . '.bienes.pdf', $asignacion->bien->id_bien) }}"
                                                                    class="btn btn-sm btn-success mt-2">
                                                                    <i class="fas fa-file-pdf"></i> PDF
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="text-center mt-3">
                                                        <a href="{{ route($prefix . '.bienes.edit', $asignacion->bien->id_bien) }}"
                                                            class="btn btn-primary btn-sm mx-2 px-4">Editar</a>
                                                        <button type="button" class="btn btn-secondary btn-sm mx-2 px-4"
                                                            data-bs-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach


                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $bienes_asignados->appends(['tab' => 'bienes'])->links() }}
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                <button type="submit" class="btn btn-success mx-2">
                                    <i class="fas fa-file-excel"></i> Exportar bienes del resguardante
                                </button>

                                <button type="button" id="descargarEtiquetas" class="btn btn-primary mx-2">
                                    <i class="fas fa-qrcode"></i> Descargar etiquetas
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <a href="{{ route("$prefix.resguardantes.index") }}" class="btn btn-secondary mx-2"> Atrás </a>
            <a href="{{ route("$prefix.resguardantes.edit", $resguardante->id_resguardante) }}"
                class="btn btn-primary mx-2"> Editar </a>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Mensajes con SweetAlert2 -->
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}',
                confirmButtonText: 'Aceptar'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonText: 'Aceptar'
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const prefix = document.body.getAttribute('data-prefix') || 'admin';

            document.getElementById('selectAll')?.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="bienes[]"]');
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            });

            document.getElementById('descargarEtiquetas')?.addEventListener('click', function() {
                let bienesSeleccionados = [];

                document.querySelectorAll('input[name="bienes[]"]:checked').forEach(checkbox => {
                    bienesSeleccionados.push(checkbox.value);
                });

                if (bienesSeleccionados.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Por favor, selecciona al menos un bien.',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                let form = document.getElementById('bienesForm');
                form.action = `/${prefix}/resguardantes/descargar-etiquetas`;
                form.method = "POST";

                let oldInput = document.getElementById("bienesInput");
                if (oldInput) oldInput.remove();

                let inputBienes = document.createElement("input");
                inputBienes.type = "hidden";
                inputBienes.name = "bienes";
                inputBienes.id = "bienesInput";
                inputBienes.value = JSON.stringify(bienesSeleccionados);
                form.appendChild(inputBienes);

                form.submit();
            });
        });
    </script>
@endsection
