@extends('layouts.app')


@section('title', 'Usuarios Registrados')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Usuarios Registrados</h2>
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
            </ol>
        </nav>
        <!-- Botón para agregar nueva dirección -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route($prefix . '.users.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Registrar nuevo usuario
            </a>
        </div>

        <!-- Buscador y selector de roles -->
        <div class="card p-3 mb-3">
            <form method="GET" action="{{ route($prefix . '.users.index') }}" class="position-relative">
                <div class="input-group">
                    <!-- Selector de Rol (Ajustado al tamaño del texto) -->

                    <select class="form-select" id="filtro-rol" name="rol" style="max-width: 250px;">
                        <option value="" {{ request('rol') == '' ? 'selected' : '' }}>Todos los roles</option>
                        <option value="Administrador" {{ request('rol') == 'Administrador' ? 'selected' : '' }}>
                            Administrador</option>
                        <option value="Operador" {{ request('rol') == 'Operador' ? 'selected' : '' }}>Operador</option>
                        <option value="Resguardante" {{ request('rol') == 'Resguardante' ? 'selected' : '' }}>Resguardante
                        </option>
                    </select>


                    <!-- Campo de búsqueda (Ajustado sin deformación) -->
                    <input type="text" class="form-control" id="buscar-usuario" name="search"
                        placeholder="Buscar por nombre o correo" value="{{ request('search') }}"
                        style="flex: 1; min-width: 200px; max-width: 100%;">

                    <!-- Botón de búsqueda -->
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>

                <!-- Contenedor de sugerencias -->
                <div id="resultados-busqueda" class="list-group position-absolute bg-white border rounded shadow-sm"
                    style="z-index: 1050; display: none; max-height: 250px; overflow-y: auto; width: 100%; top: 100%; left: 0;">
                </div>
            </form>
        </div>



        <div class="card p-4">
            <h4 class="mb-4">Lista de Usuarios</h4>

            @if ($users->isEmpty())
                <div class="alert alert-info text-center">No hay usuarios registrados.</div>
            @else
                <!-- Tabla de usuarios -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>Correo Electrónico</th>
                                <th>Rol</th>
                                <th>Resguardante Asignado</th>
                                <th>Fecha de Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="table-light">
                                    <td class="text-center">{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge
                            @switch($user->role->name)
                                @case('Administrador') bg-danger @break
                                @case('Operador') bg-warning text-dark @break
                                @default bg-success
                            @endswitch">
                                            {{ $user->role->name }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{ $user->resguardante ? $user->resguardante->nombre_apellido : 'No asignado' }}
                                    </td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-3">
                                            <!-- Ver usuario -->
                                            <a href="{{ route($prefix . '.users.show', $user->id) }}" class="text-info"
                                                title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Editar usuario (permitido solo si el usuario NO es Administrador) -->
                                            @if ($user->role->name !== 'Administrador')
                                                <a href="{{ route($prefix . '.users.edit', $user->id) }}"
                                                    class="text-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            <!-- Eliminar usuario (solo permitido para Administrador y no puede eliminar a otros administradores) -->
                                            @if (auth()->user()->role->name === 'Administrador' && $user->role->name !== 'Administrador')
                                                <form action="{{ route($prefix . '.users.destroy', $user->id) }}"
                                                    method="POST" class="delete-form d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-link text-danger p-0 border-0 eliminar-usuario"
                                                        title="Eliminar">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        #buscar-usuario {
            width: 100%;
        }

        #resultados-busqueda {
            max-height: 300px;
            /* Evita que la lista sea demasiado larga */
            overflow-y: auto;
            background: white;
            border-radius: 0.375rem;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            width: 100%;
            /* Se ajusta al tamaño del input */
        }

        .list-group-item {
            cursor: pointer;
            padding: 10px;
            transition: background 0.3s;
        }

        .list-group-item:hover {
            background: #e9ecef;
        }
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buscarUsuarioInput = document.getElementById('buscar-usuario');
            const filtroRolSelect = document.getElementById('filtro-rol');
            const resultadosBusqueda = document.getElementById('resultados-busqueda');

            // Obtener el prefijo del rol dinámicamente desde el atributo data
            const prefix = document.body.getAttribute('data-prefix') || 'admin';

            function actualizarSugerencias() {
                const query = buscarUsuarioInput.value.trim();
                const rolSeleccionado = filtroRolSelect.value;

                if (query.length < 2) {
                    resultadosBusqueda.style.display = "none";
                    return;
                }

                let url = `/${prefix}/users?search=${query}`;
                if (rolSeleccionado) {
                    url += `&role=${rolSeleccionado}`;
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

                            data.forEach(usuario => {
                                const item = document.createElement('button');
                                item.type = 'button';
                                item.classList.add('list-group-item', 'list-group-item-action',
                                    'usuario-item');
                                item.innerHTML = `
                                    <strong>${usuario.name}</strong>
                                    <small>${usuario.email}</small>
                                `;

                                item.addEventListener('click', () => {
                                    window.location.href = `/${prefix}/users/${usuario.id}`;
                                });

                                resultadosBusqueda.appendChild(item);
                            });
                        } else {
                            resultadosBusqueda.innerHTML = `
                                <div class="list-group-item text-center text-muted">No se encontraron usuarios.</div>
                            `;
                            resultadosBusqueda.style.display = "block";
                        }
                    })
                    .catch(error => console.error('Error en la búsqueda:', error));
            }

            buscarUsuarioInput.addEventListener('input', actualizarSugerencias);
        });
    </script>





    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Capturar formularios de eliminación
            document.querySelectorAll(".delete-form").forEach(form => {
                form.addEventListener("submit", function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: "¿Estás seguro?",
                        text: "Esta acción no se puede deshacer.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Sí, eliminar",
                        cancelButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Mensaje de éxito después de eliminar
            let successMessage = @json(session('success'));
            let errorMessage = @json(session('error'));

            if (successMessage) {
                Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: successMessage,
                    confirmButtonColor: "#28a745"
                });
            }

            if (errorMessage) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: errorMessage,
                    confirmButtonColor: "#d33"
                });
            }
        });
    </script>

@endsection
