@extends('layouts.app')


@section('title', 'Ver Resguardantes')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Resguardantes</h2>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Resguardantes</li>
            </ol>
        </nav>

        <!-- Mensajes con SweetAlert2 -->
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: "OK"
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ $errors->first() }}",
                    confirmButtonColor: '#d33',
                    confirmButtonText: "OK"
                });
            </script>
        @endif

        <!-- Botón para agregar nueva dirección -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route("$prefix.resguardantes.create") }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Registrar nuevo resguardante
            </a>
        </div>

        <div class="card p-3 mb-3">
            <form method="GET" action="{{ route("$prefix.resguardantes.index") }}" class="position-relative">
                <div class="input-group">
                    <!-- Selector de Dirección -->
                    <select class="form-select" id="filtro-direccion" name="direccion" style="max-width: 250px;">
                        <option value="">Todas las direcciones</option>
                        @foreach ($direcciones as $direccion)
                            <option value="{{ $direccion->id_direccion }}"
                                {{ request('direccion') == $direccion->id_direccion ? 'selected' : '' }}>
                                {{ $direccion->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Contenedor del campo de búsqueda y resultados -->

                    <!-- Campo de búsqueda -->
                    <input type="text" class="form-control" id="buscar-resguardante" name="search"
                        placeholder="Buscar por nombre o número de empleado" value="{{ request('search') }}">

                    <!-- Contenedor de resultados ajustado al ancho del input -->
                    <div id="resultados-busqueda" class="list-group position-absolute bg-white border rounded shadow-sm"
                        style="z-index: 1050; display: none; max-height: 250px; overflow-y: auto; width: calc(100% - 2px); top: 100%; left: 0;">
                    </div>

                    <!-- Botón de búsqueda -->
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>


        <div class="card p-4">
            <h4 class="mb-4">Lista de Resguardantes</h4>

            @if ($resguardantes->isEmpty())
                <div class="alert alert-info text-center">No hay resguardantes registrados.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th>N° de Personal</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Departamento</th>
                                <th>Fecha de Alta</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resguardantes as $resguardante)
                                <tr class="table-light">
                                    <td>{{ $resguardante->numero_empleado }}</td>
                                    <td>{{ $resguardante->nombre_apellido }}</td>
                                    <td>{{ $resguardante->direccion->nombre }}</td>
                                    <td>{{ $resguardante->departamento->nombre }}</td>
                                    <td>{{ \Carbon\Carbon::parse($resguardante->fecha)->format('d/m/Y') }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $resguardante->estado == 'activo' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($resguardante->estado) }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-3">
                                            <a href="{{ route("$prefix.resguardantes.show", $resguardante->id_resguardante) }}"
                                                class="text-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route("$prefix.resguardantes.edit", $resguardante->id_resguardante) }}"
                                                class="text-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form
                                                action="{{ route("$prefix.resguardantes.destroy", $resguardante->id_resguardante) }}"
                                                method="POST" class="form-eliminar d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn p-0 text-danger eliminar-resguardante"
                                                    data-nombre="{{ $resguardante->nombre_apellido }}"
                                                    data-bienes="{{ $resguardante->bienes->count() }}" title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $resguardantes->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <!-- CSRF Token para AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Ajuste de la lista de sugerencias */
        #resultados-busqueda {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            width: 100%;
            /* Se ajusta completamente al input */
            position: absolute;
            top: 100%;
            left: 0;
        }

        /* Elemento de cada sugerencia */
        .resguardante-item {
            padding: 10px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid #eee;
        }

        .resguardante-item:hover {
            background-color: #f8f9fa;
        }
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buscarResguardanteInput = document.getElementById('buscar-resguardante');
            const filtroDireccionSelect = document.getElementById('filtro-direccion');
            const resultadosBusqueda = document.getElementById('resultados-busqueda');

            // Obtener el prefijo dinámicamente desde el atributo data-prefix en el body
            const prefix = document.body.getAttribute('data-prefix') || 'admin';

            function actualizarSugerencias() {
                const query = buscarResguardanteInput.value.trim();
                const direccionSeleccionada = filtroDireccionSelect.value;

                if (query.length < 2) {
                    resultadosBusqueda.style.display = "none";
                    return;
                }

                let url = `/${prefix}/resguardantes?search=${query}`;
                if (direccionSeleccionada) {
                    url += `&direccion=${direccionSeleccionada}`;
                }

                fetch(url, {
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
                                const item = document.createElement('button');
                                item.type = 'button';
                                item.classList.add('list-group-item', 'list-group-item-action',
                                    'resguardante-item');
                                item.innerHTML = `
                                    <strong>${resguardante.nombre_apellido}</strong>
                                    <small>N° Empleado: ${resguardante.numero_empleado} | Dirección: ${resguardante.id_direccion}</small>
                                `;

                                item.addEventListener('click', () => {
                                    if (resguardante.id_resguardante) {
                                        window.location.href =
                                            `/${prefix}/resguardantes/${resguardante.id_resguardante}/ver`;
                                    } else {
                                        console.error("ID del resguardante no definido:",
                                            resguardante);
                                    }
                                });

                                resultadosBusqueda.appendChild(item);
                            });
                        } else {
                            // Agregar mensaje de "Resguardante no encontrado"
                            const mensajeNoEncontrado = document.createElement('div');
                            mensajeNoEncontrado.classList.add('list-group-item', 'text-center', 'text-muted');
                            mensajeNoEncontrado.textContent = "No se encontraron resguardantes.";
                            resultadosBusqueda.appendChild(mensajeNoEncontrado);
                            resultadosBusqueda.style.display = "block";
                        }
                    })
                    .catch(error => console.error('Error en la búsqueda:', error));
            }

            function cargarDepartamentos() {
                const direccionSeleccionada = filtroDireccionSelect.value;

                if (direccionSeleccionada) {
                    fetch(`/${prefix}/resguardantes?direccion=${direccionSeleccionada}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Departamentos cargados:", data);
                        })
                        .catch(error => console.error('Error al cargar departamentos:', error));
                }
            }

            buscarResguardanteInput.addEventListener('input', actualizarSugerencias);
            filtroDireccionSelect.addEventListener('change', cargarDepartamentos);
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.eliminar-resguardante').forEach(button => {
                button.addEventListener('click', function() {
                    const nombre = this.dataset.nombre;
                    const tieneBienes = parseInt(this.dataset.bienes) > 0;
                    const form = this.closest("form");

                    if (tieneBienes) {
                        Swal.fire({
                            icon: "error",
                            title: "No se puede eliminar",
                            text: `El resguardante ${nombre} tiene bienes asignados y no se puede eliminar.`,
                            confirmButtonColor: "#d33",
                        });
                        return;
                    }

                    Swal.fire({
                        title: `¿Eliminar resguardante ${nombre}?`,
                        text: "Esta acción no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Mensajes del Controlador con SweetAlert2
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
        });
    </script>
@endsection
