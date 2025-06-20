@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>
            Reportes eliminados de <strong>{{ Auth::user()->resguardante->nombre_apellido ?? 'No asignado' }}</strong>
        </h2>

        @if (isset($mensajeError))
            <div class="alert alert-warning text-center fw-semibold" role="alert">
                {{ $mensajeError }}
            </div>
        @else
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('resguardante.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mis reportes eliminados</li>
                </ol>
            </nav>

            <form class="mb-3 position-relative" autocomplete="off">
                <div class="input-group">
                    <input type="text" id="busquedaBienHistorial" class="form-control rounded-start"
                        placeholder="Buscar bien por nombre o número de inventario...">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div id="sugerenciasBienesHistorial"
                    class="list-group position-absolute w-100 z-3 bg-white border rounded shadow"
                    style="max-height: 180px; overflow-y: auto; display: none;"></div>
            </form>

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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($historial as $reporte)
                                <tr class="table-light">
                                    <td>
                                        <strong>{{ $reporte->bien->nombre ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">({{ $reporte->bien->numero_inventario ?? '---' }})</small>
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

                                            <a href="{{ route('resguardante.reportes.historial.descargar', $reporte->id) }}"
                                                class="btn btn-sm btn-outline-secondary" title="Descargar PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                @include('resguardante.reportes.partials.modal_reporte', ['reporte' => $reporte])
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted">No hay reportes eliminados disponibles.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $historial->links() }}
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('busquedaBienHistorial');
            const sugerencias = document.getElementById('sugerenciasBienesHistorial');

            input.addEventListener('input', function () {
                const term = this.value.trim();

                if (term.length < 2) {
                    sugerencias.innerHTML = '';
                    sugerencias.style.display = 'none';
                    return;
                }

                fetch(`/resguardante/reportes/buscar-bien-historial?term=${encodeURIComponent(term)}`)
                    .then(res => res.json())
                    .then(data => {
                        sugerencias.innerHTML = '';

                        if (data.length === 0) {
                            sugerencias.innerHTML = `<div class="list-group-item disabled">Sin resultados</div>`;
                        } else {
                            data.forEach(bien => {
                                const item = document.createElement('div');
                                item.classList.add('list-group-item', 'list-group-item-action');
                                item.textContent = `${bien.nombre} (${bien.inventario})`;
                                item.style.cursor = 'pointer';

                                item.addEventListener('click', function () {
                                    window.location.href = `/resguardante/reportes/historial?bien_id=${bien.id}`;
                                });

                                sugerencias.appendChild(item);
                            });
                        }

                        sugerencias.style.display = 'block';
                    })
                    .catch(() => {
                        sugerencias.innerHTML = '';
                        sugerencias.style.display = 'none';
                    });
            });

            document.addEventListener('click', function (e) {
                if (!sugerencias.contains(e.target) && e.target !== input) {
                    sugerencias.innerHTML = '';
                    sugerencias.style.display = 'none';
                }
            });
        });
    </script>
@endsection
