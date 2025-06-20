@extends('layouts.app')

@section('title', 'Registrar Resguardante')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2 class="text-primary">Registrar Resguardante</h2>

            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route("$prefix.resguardantes.index") }}">Resguardantes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Registrar Resguardante</li>
                </ol>
            </nav>

            <!-- Mensajes de error SOLO ARRIBA -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Formulario -->
            <div class="card p-4 shadow">
                <form id="form-registrar-resguardante" action="{{ route("$prefix.resguardantes.store") }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Campos principales -->
                        <div class="col-md-8 col-12">
                            <div class="mb-3">
                                <label for="numeroEmpleado" class="form-label fw-bold">Número de empleado</label>
                                <input type="text" class="form-control @error('numero_empleado') is-invalid @enderror"
                                    id="numeroEmpleado" name="numero_empleado"
                                    value="{{ old('numero_empleado') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="nombreApellido" class="form-label fw-bold">Nombre y Apellido</label>
                                <input type="text" class="form-control" id="nombreApellido" name="nombre_apellido"
                                    value="{{ old('nombre_apellido') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="fecha" class="form-label fw-bold">Fecha de alta de resguardante</label>
                                <input type="date" class="form-control" id="fecha" name="fecha"
                                    value="{{ old('fecha') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="direccion" class="form-label fw-bold">Dirección</label>
                                <select class="form-select" id="direccion" name="id_direccion" required>
                                    <option selected disabled>Seleccione la dirección</option>
                                    @foreach($direcciones as $direccion)
                                    <option value="{{ $direccion->id_direccion }}"
                                        {{ old('id_direccion') == $direccion->id_direccion ? 'selected' : '' }}>
                                        {{ $direccion->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="departamento" class="form-label fw-bold">Departamento</label>
                                <select class="form-select" id="departamento" name="id_departamento" required>
                                    <option selected disabled>Seleccione el departamento</option>
                                </select>
                            </div>
                        </div>

                        <!-- Sección lateral -->
                        <div class="col-md-4 col-12 mt-4 mt-md-0">
                            <div class="card mb-3 p-3">
                                <h5>Estatus</h5>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="estado" id="activo"
                                        value="activo" {{ old('estado', 'activo') == 'activo' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Activo</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="estado" id="inactivo"
                                        value="inactivo" {{ old('estado') == 'inactivo' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="inactivo">Inactivo</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label fw-bold">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono"
                                    value="{{ old('telefono') }}" placeholder="Ingrese el número de teléfono"
                                    required pattern="[0-9]{10}">
                                <small class="text-muted">Debe contener 10 dígitos.</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success mx-2" id="btnGuardar">Guardar</button>
                        <a href="{{ route("$prefix.resguardantes.index") }}" class="btn btn-danger mx-2">Atrás</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const direccionSelect = document.getElementById('direccion');
        const departamentoSelect = document.getElementById('departamento');
        const formResguardante = document.getElementById('form-registrar-resguardante');

        // Función para cargar departamentos dinámicamente
        function cargarDepartamentos(direccionId, selectedDepartamento = null) {
            if (!direccionId) return;

            departamentoSelect.innerHTML = '<option selected disabled>Cargando...</option>';
            departamentoSelect.disabled = true;

            fetch(`/departamentos/${direccionId}`)
                .then(response => response.json())
                .then(data => {
                    departamentoSelect.innerHTML = '<option selected disabled>Seleccione el departamento</option>';
                    data.forEach(departamento => {
                        const option = document.createElement('option');
                        option.value = departamento.id_departamento;
                        option.textContent = departamento.nombre;
                        if (selectedDepartamento && selectedDepartamento == departamento.id_departamento) {
                            option.selected = true;
                        }
                        departamentoSelect.appendChild(option);
                    });
                    departamentoSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error al cargar los departamentos:', error);
                    departamentoSelect.innerHTML = '<option selected disabled>Error al cargar</option>';
                });
        }

        // Evento para cargar departamentos cuando cambia la dirección
        direccionSelect.addEventListener('change', function() {
            cargarDepartamentos(this.value);
        });

        // Cargar departamentos automáticamente si hubo un error y se recargó la página
        const direccionSeleccionada = direccionSelect.value;
        const departamentoSeleccionado = "{{ old('id_departamento') }}";
        if (direccionSeleccionada) {
            cargarDepartamentos(direccionSeleccionada, departamentoSeleccionado);
        }

        // SweetAlert2 para confirmar antes de enviar el formulario
        formResguardante.addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el envío inmediato

            Swal.fire({
                title: '¿Registrar Resguardante?',
                text: 'Se guardará en el sistema.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    formResguardante.submit(); // Envía el formulario después de la confirmación
                }
            });
        });
    });
</script>

@endsection
