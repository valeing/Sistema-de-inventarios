@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Mis Reportes</h2>

        @if (isset($mensajeError))
            <div class="alert alert-warning text-center fw-semibold" role="alert">
                {{ $mensajeError }}
            </div>
        @else
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('resguardante.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mis reportes</li>
                </ol>
            </nav>

            <!-- Botón para agregar nuevo -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('reportes.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Reportar bien
                </a>
            </div>

            <!-- Buscador -->
            <form class="mb-3 position-relative" autocomplete="off">
                <div class="input-group">
                    <input type="text" id="busquedaBien" class="form-control rounded-start"
                        placeholder="Buscar bien por nombre o número de inventario...">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div id="sugerenciasBienes" class="list-group position-absolute w-100 z-3 bg-white border rounded shadow"
                    style="max-height: 180px; overflow-y: auto; display: none;"></div>
            </form>

            <!-- Tabla de reportes -->
            <div class="card p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle text-center" id="tablaReportes">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Bien</th>
                                <th>Comentario</th>
                                <th>Motivo de rechazo</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reportes as $reporte)
                                {{-- Tu contenido de reportes --}}
                                <tr class="table-light">
                                    <td>
                                        <strong>{{ $reporte->bien->nombre }}</strong><br>
                                        <small class="text-muted">({{ $reporte->bien->numero_inventario ?? '---' }})</small>

                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#comentarioModal{{ $reporte->id }}">
                                            <i class="fas fa-eye"></i> Ver comentario
                                        </button>
                                    </td>
                                    <!-- Modal Rechazo -->
                                    <div class="modal fade" id="rechazoModal{{ $reporte->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Motivo de rechazo</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>{{ $reporte->comentario_rechazo }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                    <td class="fw-semibold text-primary estado">{{ ucfirst($reporte->estatus) }}</td>
                                    <td class="d-flex justify-content-center gap-2 acciones">
                                        @if (in_array($reporte->estatus, ['rechazado', 'completado']))
                                            <form action="{{ route('reportes.eliminar', $reporte->id) }}" method="POST"
                                                class="form-eliminar" data-id="{{ $reporte->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-eliminar">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Modal Comentario -->
                                <div class="modal fade" id="comentarioModal{{ $reporte->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Comentario del reporte</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>{{ $reporte->comentario }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted text-center bg-light">
                                        No hay reportes disponibles para mostrar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $reportes->links() }}
                </div>
            </div>
        @endif
    </div>

@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('busquedaBien');
        const sugerencias = document.getElementById('sugerenciasBienes');

        input.addEventListener('input', function() {
            const term = this.value.trim();

            if (term.length < 2) {
                sugerencias.innerHTML = '';
                sugerencias.style.display = 'none';
                return;
            }

            fetch(`/resguardante/reportes/buscar-bien?term=${encodeURIComponent(term)}`)
                .then(res => res.json())
                .then(data => {
                    sugerencias.innerHTML = '';

                    if (data.length === 0) {
                        sugerencias.innerHTML =
                            `<div class="list-group-item disabled">Sin resultados</div>`;
                    } else {
                        data.forEach(bien => {
                            const item = document.createElement('div');
                            item.classList.add('list-group-item', 'list-group-item-action');
                            item.textContent = `${bien.nombre} (${bien.inventario})`;
                            item.style.cursor = 'pointer';

                            item.addEventListener('click', function() {
                                window.location.href =
                                    `/resguardante/reportes/mis-reportes?bien_id=${bien.id}`;
                            });

                            sugerencias.appendChild(item);
                        });
                    }

                    sugerencias.style.display = 'block';
                });
        });

        document.addEventListener('click', function(e) {
            if (!sugerencias.contains(e.target) && e.target !== input) {
                sugerencias.innerHTML = '';
                sugerencias.style.display = 'none';
            }
        });
    });
</script>


<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const botonesEliminar = document.querySelectorAll('.form-eliminar');

        botonesEliminar.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Evita el envío inmediato

                const id = this.dataset.id;

                Swal.fire({
                    title: `¿Eliminar reporte #${id}?`,
                    text: 'Este reporte será enviado al historial.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // Mensajes de éxito o error del controlador
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#dc3545'
            });
        @endif
    });
</script>
