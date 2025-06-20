@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
    <div class="container mt-4">
        <h2>Editar Usuario</h2>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.users.index") }}">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>

        <form action="{{ route("$prefix.users.update", $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Nombre Completo -->
            <div class="mb-3">
                <label for="name" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', $user->name) }}" required>
            </div>

            <!-- Seleccionar Rol -->
            <div class="mb-3">
                <label for="role_id" class="form-label">Rol</label>
                <select class="form-select" id="role_id" name="role_id" required>
                    <option value="1" {{ $user->role_id == 1 ? 'selected' : '' }}>Administrador</option>
                    <option value="2" {{ $user->role_id == 2 ? 'selected' : '' }}>Resguardante</option>
                    <option value="3" {{ $user->role_id == 3 ? 'selected' : '' }}>Operador</option>
                </select>
            </div>

            <!-- Buscar Resguardante (Solo para Resguardante) -->
            <div class="mb-3 position-relative" id="resguardante-search-container"
                style="display: {{ $user->role_id == 2 ? 'block' : 'none' }};">
                <label for="resguardante-search" class="form-label">Buscar Resguardante</label>
                <input type="text" class="form-control" id="resguardante-search"
                    placeholder="Escribe el nombre del resguardante"
                    value="{{ $user->resguardante ? $user->resguardante->nombre_apellido : '' }}">
                <input type="hidden" id="resguardante_id" name="resguardante_id"
                    value="{{ $user->resguardante ? $user->resguardante->id_resguardante : '' }}">

                <!-- Resultados de búsqueda -->
                <div id="resguardante-list" class="list-group shadow-sm resguardante-dropdown"></div>
            </div>

            <!-- Botón de Actualizar -->
            <button type="submit" class="btn btn-success">Actualizar</button>
            <a href="{{ route("$prefix.users.index") }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <style>
        /* 🔹 Estilos Unificados con la Vista de Registro */
        .list-group-item {
            display: flex;
            align-items: center;
            padding: 12px;
            transition: background 0.3s ease;
            border-bottom: 1px solid #ddd;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .resguardante-nombre {
            font-size: 17px;
            font-weight: bold;
            color: #000000;
        }

        .resguardante-info {
            font-size: 14px;
            color: #555;
            margin-top: 3px;
        }

        .resguardante-icon {
            font-size: 18px;
            color: #007bff;
            margin-right: 10px;
        }

        /* 🔹 Ajuste para que el dropdown se muestre bien */
        .resguardante-dropdown {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fff;
            position: absolute;
            width: 100%;
            z-index: 1050;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            display: none;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let searchTimeout;

            // Mostrar el buscador solo si el rol seleccionado es Resguardante
            $('#role_id').change(function() {
                if ($(this).val() == '2') {
                    $('#resguardante-search-container').fadeIn();
                } else {
                    $('#resguardante-search-container').fadeOut();
                    $('#resguardante-search').val('');
                    $('#resguardante_id').val('');
                    $('#resguardante-list').empty().hide();
                }
            });

            // Búsqueda en vivo del resguardante con debounce
            $('#resguardante-search').on('keyup', function() {
                clearTimeout(searchTimeout);
                let query = $(this).val().trim();

                if (query.length > 1) {
                    searchTimeout = setTimeout(() => {
                        buscarResguardantes(query);
                    }, 300); // Espera 300ms para evitar spam de requests
                } else {
                    $('#resguardante-list').empty().hide();
                }
            });

            // Función para realizar la búsqueda AJAX
            function buscarResguardantes(query) {
                $.ajax({
                    url: '{{ route("$prefix.users.buscar.resguardante") }}', // ✅ Agregar prefix dinámico
                    type: 'GET',
                    data: {
                        query: query
                    },
                    success: function(data) {
                        let listHtml = '';

                        if (data.length > 0) {
                            $.each(data, function(index, resguardante) {
                                if (index < 5) { // 🔥 Limitar a 5 resultados
                                    let departamentoNombre = resguardante.departamento ?
                                        resguardante.departamento.nombre : 'No asignado';
                                    let telefono = resguardante.telefono ? resguardante
                                        .telefono : 'No disponible';

                                    listHtml += `
                                <a href="#" class="list-group-item list-group-item-action d-flex align-items-start" data-id="${resguardante.id_resguardante}" data-name="${resguardante.nombre_apellido}">
                                    <i class="fas fa-user resguardante-icon me-2"></i>
                                    <div>
                                        <span class="resguardante-nombre fw-bold">${resguardante.nombre_apellido}</span><br>
                                        <span class="resguardante-info"><i class="fas fa-phone"></i> ${telefono}</span><br>
                                        <span class="resguardante-info"><i class="fas fa-building"></i> ${departamentoNombre}</span>
                                    </div>
                                </a>`;
                                }
                            });
                        } else {
                            listHtml =
                                `<div class="alert alert-warning p-2 text-center">No hay resguardantes disponibles.</div>`;
                        }

                        $('#resguardante-list').html(listHtml).fadeIn();
                    },
                    error: function(xhr) {
                        console.error('Error en la búsqueda:', xhr.responseText);
                    }
                });
            }

            // Seleccionar un resguardante al hacer clic en la lista
            $(document).on('click', '.list-group-item', function(e) {
                e.preventDefault();
                let resguardanteNombre = $(this).data('name');
                let resguardanteId = $(this).data('id');

                $('#resguardante_id').val(resguardanteId);
                $('#resguardante-search').val(resguardanteNombre);
                $('#resguardante-list').empty().hide();
            });

            // Cerrar el dropdown al hacer clic fuera
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#resguardante-search-container').length) {
                    $('#resguardante-list').fadeOut();
                }
            });
        });
    </script>

@endsection
