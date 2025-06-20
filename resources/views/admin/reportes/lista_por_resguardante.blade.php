@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">
            Reportes de <strong>{{ $resguardante->nombre_apellido }}</strong>
        </h2>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.reportes.index") }}">Lista de reportes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ver reportes</li>
            </ol>
        </nav>
        {{-- Buscador dentro de los reportes del resguardante --}}
        <form method="GET" class="mb-3 position-relative" autocomplete="off">
            <div class="input-group">
                <input type="text" id="busquedaBien" class="form-control rounded-start"
                    placeholder="Buscar por nombre de bien o número de inventario...">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div id="sugerenciasBienes" class="list-group position-absolute w-100 bg-white border rounded"
                style="max-height: 180px; overflow-y: auto; z-index: 1000; display: none;"></div>
        </form>

        {{-- ALERTAS SWEETALERT --}}
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#3085d6'
                    });
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '{{ session('error') }}',
                        confirmButtonColor: '#dc3545'
                    });
                });
            </script>
        @endif

        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle text-center">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Bien</th>
                            <th>Comentario del resguardante</th>
                            <th>Motivo de rechazo</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                            <th>Actualizar estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportes as $reporte)
                            <tr class="table-light">
                                <td>
                                    <strong>{{ $reporte->bien->nombre }}</strong><br>
                                    <small>
                                        <a href="{{ route("$prefix.bienes.show", $reporte->bien->id_bien) }}"
                                            class="text-primary text-decoration-underline" title="Ver detalles del bien">
                                            ({{ $reporte->bien->numero_inventario }})
                                        </a>
                                    </small>
                                </td>


                                {{-- Comentario del resguardante con botón --}}
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#comentarioModal{{ $reporte->id }}">
                                        <i class="fas fa-eye"></i> Ver comentario
                                    </button>
                                </td>

                                {{-- Motivo de rechazo --}}
                                <td>
                                    @if ($reporte->comentario_rechazo)
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#rechazoModal{{ $reporte->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>

                                <td>{{ $reporte->fecha_reporte }}</td>
                                <td class="fw-semibold text-primary">{{ ucfirst($reporte->estatus) }}</td>

                                {{-- Acciones: Detalles + PDF --}}
                                <td>
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                            data-bs-target="#modalDetalles{{ $reporte->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <a href="{{ route("$prefix.reportes.descargar", $reporte->id) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-download"></i>
                                        </a>

                                    </div>

                                </td>

                                {{-- Formulario para actualizar estado --}}
                                <td>
                                    <form method="POST" action="{{ route("$prefix.reportes.update", $reporte->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="d-flex flex-column align-items-center">
                                            <select name="estatus"
                                                class="form-select form-select-sm mb-2 w-auto text-center status-select"
                                                data-reporte-id="{{ $reporte->id }}">
                                                <option value="en proceso"
                                                    {{ $reporte->estatus == 'en proceso' ? 'selected' : '' }}>En proceso
                                                </option>
                                                <option value="completado"
                                                    {{ $reporte->estatus == 'completado' ? 'selected' : '' }}>Completado
                                                </option>
                                                <option value="rechazado"
                                                    {{ $reporte->estatus == 'rechazado' ? 'selected' : '' }}>Rechazado
                                                </option>
                                            </select>

                                            <button type="submit" class="btn btn-success btn-sm w-100">Guardar</button>

                                            {{-- Botón para comentario de rechazo --}}
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm mt-2 w-100 comentario-btn"
                                                data-bs-toggle="modal" data-bs-target="#modalComentario{{ $reporte->id }}"
                                                style="{{ $reporte->estatus === 'rechazado' ? '' : 'display: none;' }}">
                                                <i class="fas fa-comment-alt"></i> Comentario
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>

                            {{-- Modal: Comentario del resguardante --}}
                            <div class="modal fade" id="comentarioModal{{ $reporte->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Comentario del resguardante</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>{{ $reporte->comentario }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal: Comentario de rechazo --}}
                            <div class="modal fade" id="rechazoModal{{ $reporte->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Motivo de rechazo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>{{ $reporte->comentario_rechazo }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal: Agregar nuevo comentario de rechazo --}}
                            <div class="modal fade" id="modalComentario{{ $reporte->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route("$prefix.reportes.update", $reporte->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="estatus" value="rechazado">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Motivo de rechazo</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <textarea name="comentario_rechazo" class="form-control" rows="4"
                                                    placeholder="Escribe el motivo del rechazo (opcional)...">{{ old('comentario_rechazo') }}</textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Modal: Detalles del Reporte y Bien --}}
                            <div class="modal fade" id="modalDetalles{{ $reporte->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detalles del Reporte y del Bien</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body d-flex gap-4">
                                            <!-- Detalles del Reporte -->
                                            <div class="border rounded p-3 w-50">
                                                <h6 class="text-primary">Detalles del Reporte</h6>
                                                <p><strong>Comentario del
                                                        resguardante:</strong><br>{{ $reporte->comentario }}</p>
                                                <p><strong>Motivo de
                                                        rechazo:</strong><br>{{ $reporte->comentario_rechazo ?? '---' }}
                                                </p>
                                                <p><strong>Fecha del reporte:</strong><br>{{ $reporte->fecha_reporte }}</p>
                                                <p><strong>Estado:</strong><br>{{ ucfirst($reporte->estatus) }}</p>
                                            </div>

                                            <!-- Detalles del Bien -->
                                            <div class="border rounded p-3 w-50">
                                                <h6 class="text-success">Detalles del Bien</h6>
                                                <p><strong>Nombre:</strong><br>{{ $reporte->bien->nombre }}</p>
                                                <p><strong>Estado:</strong><br>{{ ucfirst($reporte->bien->estado) }}</p>
                                                <p><strong>Departamento:</strong><br>{{ $reporte->bien->departamento->nombre ?? '---' }}
                                                </p>
                                                <p><strong>Categoría:</strong><br>{{ $reporte->bien->categoria }}</p>
                                                <p><strong>Fecha de
                                                        Adquisición:</strong><br>{{ $reporte->bien->fecha_adquisicion }}
                                                </p>
                                                <p><strong>N° de Serie:</strong><br>{{ $reporte->bien->numero_serie }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $reportes->links() }}
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('busquedaBien');
            const sugerencias = document.getElementById('sugerenciasBienes');
            const resguardanteId = '{{ $resguardante->id_resguardante }}';
            const prefix = '{{ $prefix }}';

            input.addEventListener('input', function() {
                const valor = this.value.trim();
                if (valor.length < 2) {
                    sugerencias.innerHTML = '';
                    sugerencias.style.display = 'none';
                    return;
                }

                fetch(`/${prefix}/reportes/${resguardanteId}/buscar-bien?term=${encodeURIComponent(valor)}`)
                    .then(response => response.json())
                    .then(data => {
                        sugerencias.innerHTML = '';
                        if (data.length === 0) {
                            sugerencias.innerHTML =
                                `<div class="list-group-item disabled">No se encontraron resultados</div>`;
                        } else {
                            data.forEach(item => {
                                const div = document.createElement('div');
                                div.className = 'list-group-item list-group-item-action';
                                div.style.cursor = 'pointer';
                                div.textContent = `${item.nombre} (${item.inventario})`;
                                div.addEventListener('click', () => {
                                    window.location.href =
                                        `/${prefix}/reportes/${resguardanteId}/ver?bien=${encodeURIComponent(item.inventario)}`;
                                });
                                sugerencias.appendChild(div);
                            });
                        }
                        sugerencias.style.display = 'block';
                    });
            });

            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !sugerencias.contains(e.target)) {
                    sugerencias.innerHTML = '';
                    sugerencias.style.display = 'none';
                }
            });
        });
    </script>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
    <script>
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                const form = this.closest('form');
                const comentarioBtn = form.querySelector('.comentario-btn');
                comentarioBtn.style.display = this.value === 'rechazado' ? 'inline-block' : 'none';
            });
        });
    </script>
@endpush
