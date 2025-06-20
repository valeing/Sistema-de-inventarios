@extends('layouts.app')


@section('title', 'Editar Resguardante')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2>Editar Resguardante</h2>

            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route("$prefix.resguardantes.index") }}">Resguardantes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar Resguardante</li>
                </ol>
            </nav>

            <!-- Mensaje de éxito -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Mensajes de error -->
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Contenedor principal -->
            <div class="card p-4">
                <form id="form-editar-resguardante" action="{{ route("$prefix.resguardantes.update", $resguardante->id_resguardante) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Campos principales -->
                        <div class="col-md-8 col-12">
                            <div class="mb-3">
                                <label for="numeroEmpleado" class="form-label">Número de empleado</label>
                                <input type="text" class="form-control @error('numero_empleado') is-invalid @enderror" id="numeroEmpleado" name="numero_empleado" value="{{ old('numero_empleado', $resguardante->numero_empleado) }}" required>
                                @error('numero_empleado')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nombreApellido" class="form-label">Nombre y Apellido</label>
                                <input type="text" class="form-control @error('nombre_apellido') is-invalid @enderror" id="nombreApellido" name="nombre_apellido" value="{{ old('nombre_apellido', $resguardante->nombre_apellido) }}" required>
                                @error('nombre_apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha alta de resguardante</label>
                                <input type="date" class="form-control @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{ old('fecha', \Carbon\Carbon::parse($resguardante->fecha)->format('Y-m-d')) }}" required>
                                @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <select class="form-select @error('id_direccion') is-invalid @enderror" id="direccion" name="id_direccion" required>
                                    <option selected disabled>Seleccione la dirección</option>
                                    @foreach($direcciones as $direccion)
                                    <option value="{{ $direccion->id_direccion }}" {{ old('id_direccion', $resguardante->id_direccion) == $direccion->id_direccion ? 'selected' : '' }}>
                                        {{ $direccion->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('id_direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="departamento" class="form-label">Departamento</label>
                                <select class="form-select @error('id_departamento') is-invalid @enderror" id="departamento" name="id_departamento" required>
                                    <option selected disabled>Seleccione el departamento</option>
                                    @foreach($departamentos as $departamento)
                                    <option value="{{ $departamento->id_departamento }}" {{ old('id_departamento', $resguardante->id_departamento) == $departamento->id_departamento ? 'selected' : '' }}>
                                        {{ $departamento->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('id_departamento')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Sección lateral -->
                        <div class="col-md-4 col-12 mt-4 mt-md-0">
                            <!-- Estado del resguardante -->
                            <div class="card mb-3 p-3">
                                <h5>Estatus</h5>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="estado" id="activo" value="activo" {{ old('estado', $resguardante->estado) == 'activo' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Activo</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="estado" id="inactivo" value="inactivo" {{ old('estado', $resguardante->estado) == 'inactivo' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="inactivo">Inactivo</label>
                                </div>
                                @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Información de contacto -->
                            <div class="card p-3">
                                <h5>Información de contacto</h5>
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" value="{{ old('telefono', $resguardante->telefono) }}" placeholder="Ingrese el número de teléfono" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Botones en la parte inferior -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary mx-2" form="form-editar-resguardante">Guardar</button>
                    <a href="{{ route("$prefix.resguardantes.index") }}" class="btn btn-danger mx-2">Atrás</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para capitalización y carga dinámica -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nombreApellidoInput = document.getElementById('nombreApellido');
        const direccionSelect = document.getElementById('direccion');
        const departamentoSelect = document.getElementById('departamento');

        // Capitalizar la primera letra de cada palabra
        nombreApellidoInput.addEventListener('input', function() {
            nombreApellidoInput.value = nombreApellidoInput.value
                .toLowerCase()
                .replace(/\b\w/g, char => char.toUpperCase());
        });

        // Cargar departamentos dinámicamente
        direccionSelect.addEventListener('change', function() {
            const direccionId = this.value;

            departamentoSelect.innerHTML = '<option selected disabled>Cargando departamentos...</option>';
            departamentoSelect.disabled = true;

            fetch(`/departamentos/${direccionId}`)
                .then(response => response.json())
                .then(data => {
                    departamentoSelect.innerHTML = '<option selected disabled>Seleccione el departamento</option>';
                    data.forEach(departamento => {
                        const option = document.createElement('option');
                        option.value = departamento.id_departamento;
                        option.textContent = departamento.nombre;
                        departamentoSelect.appendChild(option);
                    });
                    departamentoSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error al cargar los departamentos:', error);
                    departamentoSelect.innerHTML = '<option selected disabled>Error al cargar</option>';
                });
        });
    });
</script>
@endsection
