@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Reportes de Bienes</h2>
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lista de reportes</li>
            </ol>
        </nav>

        @php
            $prefix = request()->segment(1); // 'admin' u 'operador'
        @endphp

        <!-- Buscador -->
        <form method="GET" class="mb-3 position-relative" autocomplete="off">
            <div class="input-group">
                <input type="text" id="busquedaResguardante" class="form-control rounded-start"
                    placeholder="Buscar resguardante por nombre...">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div id="sugerenciasResguardantes" class="list-group position-absolute w-100 z-3 bg-white border rounded"
                style="max-height: 180px; overflow-y: auto; display: none;"></div>
        </form>


        <!-- Tabla de reportes -->
        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle text-center" id="tablaReportes">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-uppercase">Resguardante</th>
                            <th class="text-uppercase">Reportes</th>
                            <th class="text-uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTabla">
                        @if ($reportes->isEmpty())
                            <tr>
                                <td colspan="3" class="text-muted">No hay reportes disponibles.</td>
                            </tr>
                        @else
                            @foreach ($reportes->groupBy('resguardante_id') as $resguardanteId => $grupo)
                                @php $resguardante = $grupo->first()->resguardante; @endphp
                                <tr class="table-light">
                                    <td class="fw-bold">{{ $resguardante->nombre_apellido ?? 'No asignado' }}</td>
                                    <td><span class="badge bg-info text-dark fs-6">{{ $grupo->count() }}</span></td>
                                    <td>
                                        <a href="{{ route("$prefix.reportes.verPorResguardante", $resguardante->id_resguardante) }}"
                                            class="btn btn-outline-primary btn-sm" title="Ver reportes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            <div class="mt-4">{{ $reportes->links() }}</div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('busquedaResguardante');
            const sugerencias = document.getElementById('sugerenciasResguardantes');
            const prefix = '{{ $prefix }}';

            input.addEventListener('input', function() {
                const valor = this.value.trim();
                if (valor.length < 2) {
                    sugerencias.innerHTML = '';
                    sugerencias.style.display = 'none';
                    return;
                }

                fetch(`/${prefix}/reportes/buscar-resguardantes?term=${encodeURIComponent(valor)}`)
                    .then(res => res.json())
                    .then(data => {
                        sugerencias.innerHTML = '';
                        if (data.length === 0) {
                            sugerencias.innerHTML =
                                '<div class="list-group-item disabled">No se encontraron resultados</div>';
                        } else {
                            data.forEach(r => {
                                const item = document.createElement('div');
                                item.classList.add('list-group-item', 'list-group-item-action');
                                item.textContent = r.nombre_apellido;
                                item.style.cursor = 'pointer';
                                item.addEventListener('click', function() {
                                    window.location.href =
                                        `/${prefix}/reportes?resguardante_id=${r.id_resguardante}`;
                                });
                                sugerencias.appendChild(item);
                            });
                        }
                        sugerencias.style.display = 'block';
                    })
                    .catch(err => {
                        console.error('Error en la búsqueda:', err);
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

@endsection
