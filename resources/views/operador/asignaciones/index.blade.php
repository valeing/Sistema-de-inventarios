@extends('layouts.app')


@section('title', 'Gestión de asignaciones')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Asignaciones</h2>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Asignaciones</li>
            </ol>
        </nav>

        <!-- Botón para agregar nueva dirección -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route("$prefix.asignaciones.create") }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Registrar nueva asignación
            </a>
        </div>
        <!-- Buscador de Resguardantes -->

        <div class="card p-3 mb-3">
            <div class="row justify-content-center align-items-center">
                <!-- Campo de búsqueda más largo -->
                <div class="position-relative">
                    <input type="text" class="form-control buscador-resguardante" id="buscar-resguardante"
                        placeholder="Buscar resguardante por nombre">
                    <div id="resultados-busqueda" class="list-group position-absolute w-100 shadow"
                        style="z-index: 1050; display: none;"></div>
                </div>
            </div>
        </div>






        <div class="card p-4 mt-3 shadow-sm">
            <h4 class="mb-4">Asignaciones registradas</h4>

            @if ($asignaciones->isEmpty())
                <div class="alert alert-info text-center">
                    No hay asignaciones registradas.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle text-center">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="text-uppercase">Resguardante</th>
                                <th class="text-uppercase">Cantidad de Bienes</th>
                                <th class="text-uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($asignaciones as $asignacion)
                                @if ($asignacion->bienes_count > 0)
                                    <tr class="table-light">
                                        <td>
                                            <div class="text-truncate" style="max-width: 250px;"
                                                title="{{ $asignacion->nombre_apellido }}">
                                                {{ $asignacion->nombre_apellido }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-dark fs-6">
                                                {{ $asignacion->bienes_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-3">
                                                <a href="{{ route("$prefix.asignaciones.show", $asignacion->id_resguardante) }}"
                                                    class="text-info" title="Ver bienes">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if (auth()->user()->role->name === 'Administrador')
                                                    <button class="btn p-0 text-danger delete-btn"
                                                        data-url="{{ route("$prefix.asignaciones.desasignarTodos", $asignacion->id_resguardante) }}"
                                                        title="Desasignar todos los bienes">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @else
                                                    <i class="fas fa-lock text-muted"
                                                        title="Solo el administrador puede desasignar todos los bienes"></i>
                                                @endif
                                            </div>
                                        </td>

                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $asignaciones->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .buscador-resguardante {
            width: 100%;
            max-width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        #resultados-busqueda {
            max-height: 250px;
            overflow-y: auto;
            background: white;
            border-radius: 5px;
        }

        .list-group-item {
            cursor: pointer;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buscarResguardanteInput = document.getElementById('buscar-resguardante');
            const resultadosBusqueda = document.getElementById('resultados-busqueda');

            function actualizarSugerencias() {
                const query = buscarResguardanteInput.value.trim();

                if (query.length < 2) {
                    resultadosBusqueda.style.display = "none";
                    return;
                }

                fetch(`/admin/asignaciones?search=${query}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        resultadosBusqueda.innerHTML = '';

                        if (data.length > 0) {
                            resultadosBusqueda.style.display = "block";

                            data.forEach(resguardante => {
                                if (!resguardante.id_resguardante) return; // Evita errores si no hay ID

                                const item = document.createElement('button');
                                item.type = 'button';
                                item.classList.add('list-group-item', 'list-group-item-action');
                                item.innerHTML = `
                                <strong>${resguardante.nombre_apellido}</strong>
                                <small class="d-block">Bienes asignados: ${resguardante.bienes_count}</small>
                            `;

                                item.addEventListener('click', () => {
                                    if (resguardante.id_resguardante) {
                                        window.location.href =
                                            `/admin/asignaciones/${resguardante.id_resguardante}/ver`;
                                    } else {
                                        alert('Error: No se encontró el ID del resguardante.');
                                    }
                                });

                                resultadosBusqueda.appendChild(item);
                            });
                        } else {
                            resultadosBusqueda.innerHTML = `
                            <div class="list-group-item text-center text-muted">
                                No se encontró ningún resguardante.
                            </div>
                        `;
                            resultadosBusqueda.style.display = "block";
                        }
                    })
                    .catch(error => console.error('Error en la búsqueda:', error));
            }

            buscarResguardanteInput.addEventListener('input', actualizarSugerencias);

            document.addEventListener("click", function(event) {
                if (!buscarResguardanteInput.contains(event.target) && !resultadosBusqueda.contains(event
                        .target)) {
                    resultadosBusqueda.style.display = "none";
                }
            });
        });
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    let deleteUrl = this.dataset.url;
                    Swal.fire({
                        title: "¿Estás seguro?",
                        text: "Se desasignarán todos los bienes de este resguardante.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Sí, eliminar",
                        cancelButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let form = document.createElement('form');
                            form.method = 'POST';
                            form.action = deleteUrl;

                            let csrfToken = document.querySelector(
                                'meta[name="csrf-token"]').content;
                            let csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;

                            let methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'DELETE';

                            form.appendChild(csrfInput);
                            form.appendChild(methodInput);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        });

        // SweetAlert2 con botón OK en los mensajes del controlador
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: "OK"
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
                confirmButtonText: "OK"
            });
        @endif
    </script>
@endsection
