@extends('layouts.resguardante')

@section('title', 'Mis Bienes Asignados')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Mis Bienes Asignados</h2>
            <a href="{{ route('resguardante.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>

        {{-- Formulario de búsqueda (GET) --}}
        <form method="GET" action="{{ route('resguardante.mis-bienes') }}" class="mb-3 position-relative" autocomplete="off">
            <div class="input-group">
                <input type="text" id="busquedaBien" name="term" value="{{ request('term') }}"
                    class="form-control rounded-start"
                    placeholder="Buscar bien por nombre, número de inventario o serie...">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div id="sugerenciasBienes" class="list-group position-absolute w-100 z-3 bg-white border rounded shadow"
                style="max-height: 180px; overflow-y: auto; display: none;"></div>
        </form>

        <div class="card p-4">
            @if (isset($mensaje))
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i> {{ $mensaje }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Bien</th>
                                <th>Estado</th>
                                <th>Departamento</th>
                                <th>Fecha de Adquisición</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bienes as $bien)
                                <tr>
                                    <td>{{ $bien->id_bien }}</td>
                                    <td>{{ $bien->nombre }}</td>
                                    <td>
                                        <span
                                            class="badge
                                        @switch($bien->estado)
                                            @case('activo') bg-success @break
                                            @case('mantenimiento') bg-warning text-dark @break
                                            @default bg-danger
                                        @endswitch">
                                            {{ ucfirst($bien->estado ?? 'No definido') }}
                                        </span>
                                    </td>
                                    <td>{{ $bien->departamento->nombre ?? 'No asignado' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bien->fecha_adquisicion)->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('resguardante.bienes.show', ['id' => $bien->id_bien]) }}"
                                            class="text-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No tienes bienes asignados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $bienes->appends(['term' => request('term')])->links() }}
                </div>
            @endif
        </div>
    </div>


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

                fetch(`{{ route('resguardante.bienes.buscar') }}?term=${encodeURIComponent(term)}`)
                    .then(res => res.json())
                    .then(data => {
                        sugerencias.innerHTML = '';

                        if (!Array.isArray(data) || data.length === 0) {
                            sugerencias.innerHTML =
                                `<div class="list-group-item disabled">Sin resultados</div>`;
                            sugerencias.style.display = 'block';
                            return;
                        }

                        data.forEach(bien => {
                            if (!bien.id) return;

                            const enlace = document.createElement('a');
                            enlace.href = `/resguardante/mis-bienes/${bien.id}`;
                            enlace.classList.add('list-group-item', 'list-group-item-action');
                            enlace.textContent =
                                `${bien.nombre} (${bien.inventario ?? 'Sin inventario'}) [${bien.estado}]`;

                            sugerencias.appendChild(enlace);
                        });

                        sugerencias.style.display = 'block';
                    })
                    .catch(err => {
                        console.error('Error al obtener sugerencias:', err);
                        sugerencias.innerHTML = '';
                        sugerencias.style.display = 'none';
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
