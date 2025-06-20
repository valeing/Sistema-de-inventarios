@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">
            Reportes eliminados de <strong>{{ $resguardante->nombre_apellido }}</strong>
        </h2>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.reportes.index") }}">Lista de reportes eliminados</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Ver reportes eliminados</li>
            </ol>
        </nav>

        {{-- SweetAlert para mensajes del controlador --}}
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
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
        <form class="mb-3 position-relative" autocomplete="off">
            <div class="input-group">
                <input type="text" id="busquedaBien" class="form-control rounded-start"
                    placeholder="Buscar bien por nombre o inventario...">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div id="sugerenciasBienes" class="list-group position-absolute w-100 z-3 bg-white border rounded shadow"
                style="max-height: 180px; overflow-y: auto; display: none;"></div>
        </form>


        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle text-center">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-uppercase">Bien</th>
                            <th class="text-uppercase">Comentario del resguardante</th>
                            <th class="text-uppercase">Motivo de rechazo</th>
                            <th class="text-uppercase">Fecha</th>
                            <th class="text-uppercase">Estado</th>
                            <th class="text-uppercase">Acciones</th>
                            <th class="text-uppercase">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historial as $reporte)
                            <tr class="table-light">
                                <td>
                                    <strong>{{ $reporte->bien->nombre ?? 'N/A' }}</strong><br>
                                    <small>
                                        @if ($reporte->bien && $reporte->bien->id_bien)
                                            <a href="{{ route("$prefix.bienes.show", $reporte->bien->id_bien) }}"
                                                class="text-primary text-decoration-underline"
                                                title="Ver detalles del bien">
                                                ({{ $reporte->bien->numero_inventario ?? '---' }})
                                            </a>
                                        @else
                                            <span class="text-muted">(---)</span>
                                        @endif
                                    </small>
                                </td>


                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#comentarioModal{{ $reporte->id }}">
                                        <i class="fas fa-eye"></i> Ver comentario
                                    </button>
                                </td>

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

                                <td>
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                            data-bs-target="#modalDetalles{{ $reporte->id }}" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <a href="{{ route("$prefix.reportes.historial.descargar", $reporte->id) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Descargar PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>

                                <td>
                                    <form action="{{ route("$prefix.reportes.historial.eliminar", $reporte->id) }}"
                                        method="POST" class="form-eliminar-definitivo">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger btn-eliminar-definitivo"
                                            data-id="{{ $reporte->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Comentario -->
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

                            <!-- Modal Rechazo -->
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

                            <!-- Modal Detalles -->
                            <div class="modal fade" id="modalDetalles{{ $reporte->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detalles del Reporte y del Bien</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body d-flex gap-4">
                                            <div class="border rounded p-3 w-50">
                                                <h6 class="text-primary">Detalles del Reporte</h6>
                                                <p><strong>Comentario:</strong><br>{{ $reporte->comentario }}</p>
                                                <p><strong>Motivo de
                                                        rechazo:</strong><br>{{ $reporte->comentario_rechazo ?? '---' }}
                                                </p>
                                                <p><strong>Fecha:</strong><br>{{ $reporte->fecha_reporte }}</p>
                                                <p><strong>Estado:</strong><br>{{ ucfirst($reporte->estatus) }}</p>
                                            </div>
                                            <div class="border rounded p-3 w-50">
                                                <h6 class="text-success">Detalles del Bien</h6>
                                                <p><strong>Nombre:</strong><br>{{ $reporte->bien->nombre }}</p>
                                                <p><strong>Estado:</strong><br>{{ ucfirst($reporte->bien->estado) }}</p>
                                                <p><strong>Departamento:</strong><br>{{ $reporte->bien->departamento->nombre ?? '---' }}
                                                </p>
                                                <p><strong>Categoría:</strong><br>{{ $reporte->bien->categoria }}</p>
                                                <p><strong>Fecha de
                                                        adquisición:</strong><br>{{ $reporte->bien->fecha_adquisicion }}
                                                </p>
                                                <p><strong>N° Serie:</strong><br>{{ $reporte->bien->numero_serie }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted">No hay reportes eliminados de este resguardante.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $historial->links() }}
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
                const term = input.value.trim();

                if (term.length < 2) {
                    sugerencias.innerHTML = '';
                    sugerencias.style.display = 'none';
                    return;
                }

                fetch(
                        `/${prefix}/reportes/historial/${resguardanteId}/buscar-bien?term=${encodeURIComponent(term)}`
                    )
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
                                    // Redirigir pasando el ID del bien como parámetro
                                    window.location.href =
                                        `/${prefix}/reportes/historial/${resguardanteId}/ver?bien_id=${bien.id}`;
                                });

                                sugerencias.appendChild(item);
                            });
                        }

                        sugerencias.style.display = 'block';
                    })
                    .catch(error => console.error('Error en sugerencias:', error));
            });

            // Ocultar sugerencias al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!sugerencias.contains(e.target) && e.target !== input) {
                    sugerencias.innerHTML = '';
                    sugerencias.style.display = 'none';
                }
            });
        });
    </script>
@endsection


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-eliminar-definitivo').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('.form-eliminar-definitivo');
                const id = this.dataset.id;

                Swal.fire({
                    title: `¿Eliminar reporte #${id}?`,
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
