@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">
            Reportes Eliminados por Resguardantes
        </h2>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lista de reportes eliminados</li>
            </ol>
        </nav>

        <form method="GET" class="mb-3 position-relative" autocomplete="off">
            <div class="input-group">
                <input type="text" id="busquedaHistorial" class="form-control rounded-start"
                    placeholder="Buscar resguardante por nombre...">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div id="sugerenciasHistorial" class="list-group position-absolute w-100 z-3 bg-white border rounded"
                style="max-height: 200px; overflow-y: auto; display: none;"></div>
        </form>


        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle text-center">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-uppercase">Resguardante</th>
                            <th class="text-uppercase">Total reportes eliminados</th>
                            <th class="text-uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resguardantes as $resguardante)
                            <tr class="table-light">
                                <td class="fw-bold">{{ $resguardante->nombre_apellido }}</td>
                                <td>
                                    <span class="badge bg-info text-dark fs-6">
                                        {{ $resguardante->reportes_historial_count }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route("$prefix.reportes.historial.ver", $resguardante->id_resguardante) }}"
                                        class="btn btn-outline-primary btn-sm" title="Ver reportes eliminados">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-muted">No hay reportes eliminados registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $resguardantes->links() }}
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('busquedaHistorial');
            const sugerencias = document.getElementById('sugerenciasHistorial');
            const prefix = '{{ $prefix }}';

            input.addEventListener('input', function() {
                const valor = this.value.trim();
                if (valor.length < 2) {
                    sugerencias.innerHTML = '';
                    sugerencias.style.display = 'none';
                    return;
                }

                fetch(
                        `/${prefix}/reportes/historial/buscar-resguardantes?term=${encodeURIComponent(valor)}`)
                    .then(response => response.json())
                    .then(data => {
                        sugerencias.innerHTML = '';
                        if (data.length === 0) {
                            sugerencias.innerHTML =
                                '<div class="list-group-item disabled">No se encontraron resultados</div>';
                        } else {
                            data.forEach(resguardante => {
                                const item = document.createElement('div');
                                item.classList.add('list-group-item', 'list-group-item-action');
                                item.textContent = resguardante.nombre_apellido;
                                item.style.cursor = 'pointer';

                                item.addEventListener('click', () => {
                                    window.location.href =
                                        `/${prefix}/reportes/historial?resguardante=${encodeURIComponent(resguardante.nombre_apellido)}`;
                                });

                                sugerencias.appendChild(item);
                            });
                        }
                        sugerencias.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error al buscar resguardantes:', error);
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
