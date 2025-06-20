@extends('layouts.app')

@section('content')
    {{-- Mensajes emergentes --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    <div class="container">
        <h2>Registrar Usuario</h2>
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.users.index") }}">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registras usuario</li>
            </ol>
        </nav>

        <form action="{{ route("$prefix.users.store") }}" method="POST">
            @csrf

            <!-- Nombre Completo -->
            <div class="mb-3">
                <label for="name" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Correo Electrónico -->
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                    name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Contraseña -->
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                    name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirmar Contraseña -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    required>
            </div>

            <!-- Seleccionar Rol -->
            <div class="mb-3">
                <label for="role_id" class="form-label">Rol</label>
                <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                    <option value="1" {{ old('role_id') == 1 ? 'selected' : '' }}>Administrador</option>
                    <option value="3" {{ old('role_id') == 3 ? 'selected' : '' }}>Operador</option>
                    <option value="2" {{ old('role_id') == 2 ? 'selected' : '' }}>Resguardante</option>
                </select>
                @error('role_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Buscar Resguardante (Solo para Resguardante) -->
            <div class="mb-3 position-relative" id="resguardante-search-container" style="display: none;">
                <label for="resguardante-search" class="form-label">Buscar Resguardante</label>
                <input type="text" class="form-control @error('resguardante_id') is-invalid @enderror"
                    id="resguardante-search" placeholder="Escribe el nombre del resguardante"
                    value="{{ old('resguardante-search') }}">
                <input type="hidden" id="resguardante_id" name="resguardante_id" value="{{ old('resguardante_id') }}">
                @error('resguardante_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <!-- Resultados de búsqueda -->
                <div id="resguardante-list" class="list-group shadow-sm"></div>
            </div>

            <div class="d-flex justify-content-center">
                <!-- Botón de Registro -->
                <button type="submit" class="btn btn-success mx-2">Registrar</button>
                <a href="{{ route("$prefix.users.index") }}" class="btn btn-danger mx-2">Cancelar</a>
            </div>
        </form>
    </div>

    {{-- ... (el resto del código de estilos y scripts permanece igual) --}}
    <style>
        /* 🔹 Mejor visibilidad de resultados con Bootstrap */
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

        /* Estilo de la lista para que se ajuste mejor */
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
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let prefix = $('body').attr('data-prefix') || 'admin'; // Obtener el prefijo dinámico

            // Mostrar el buscador solo si el rol seleccionado es Resguardante
            $('#role_id').change(function() {
                if ($(this).val() == '2') {
                    $('#resguardante-search-container').show();
                } else {
                    $('#resguardante-search-container').hide();
                    $('#resguardante-search').val('');
                    $('#resguardante_id').val('');
                    $('#resguardante-list').html('');
                }
            });

            // Búsqueda en vivo del resguardante
            $('#resguardante-search').on('keyup', function() {
                let query = $(this).val();
                if (query.length > 1) {
                    $.ajax({
                        url: `/${prefix}/users/buscar-resguardante`, // 🔥 Ruta dinámica
                        type: 'GET',
                        data: {
                            query: query
                        },
                        success: function(data) {
                            let listHtml = '';

                            if (data.length > 0) {
                                listHtml = '';
                                $.each(data, function(index, resguardante) {
                                    if (index < 5) { // 🔥 Limitar a 5 resultados
                                        let departamentoNombre = resguardante
                                            .departamento ? resguardante.departamento
                                            .nombre : 'No asignado';
                                        let telefono = resguardante.telefono ?
                                            resguardante.telefono : 'No disponible';

                                        listHtml += `
                                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-start" data-id="${resguardante.id_resguardante}" data-name="${resguardante.nombre_apellido}">
                                        <i class="fas fa-user resguardante-icon"></i>
                                        <div>
                                            <span class="resguardante-nombre">${resguardante.nombre_apellido}</span><br>
                                            <span class="resguardante-info"><i class="fas fa-phone"></i> Tel: ${telefono}</span><br>
                                            <span class="resguardante-info"><i class="fas fa-building"></i> Departamento: ${departamentoNombre}</span>
                                        </div>
                                    </a>`;
                                    }
                                });
                            } else {
                                listHtml =
                                    `<div class="alert alert-warning p-2 text-center">No hay resguardantes disponibles.</div>`;
                            }

                            $('#resguardante-list').html(listHtml);
                        }
                    });
                } else {
                    $('#resguardante-list').html('');
                }
            });

            // Seleccionar un resguardante al hacer clic en la lista
            $(document).on('click', '.list-group-item', function() {
                let resguardanteNombre = $(this).data('name');
                let resguardanteId = $(this).data('id');

                $('#resguardante_id').val(resguardanteId);
                $('#resguardante-search').val(resguardanteNombre);
                $('#resguardante-list').html('');
            });
        });
    </script>
@endsection
