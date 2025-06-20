@extends('layouts.app')


@section('title', 'Registrar bienes')

@section('content')

    <div class="container mt-5">
        <div class="row">
            <h2 class="text-primary">Registrar bienes</h2>

            <div class="col-12">
                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route("$prefix.bienes.index") }}">Bienes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Registrar bien</li>
                    </ol>
                </nav>

                <!-- Contenedor principal -->
                <div class="card p-4 shadow">
                    <!-- Mensajes de éxito y error -->
                    @if (session('success'))
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: "{{ session('success') }}",
                                confirmButtonColor: '#28a745',
                            });
                        </script>
                    @endif

                    @if (session('error'))
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: "{{ session('error') }}",
                                confirmButtonColor: '#d33',
                            });
                        </script>
                    @endif

                    <!-- Formulario con método POST y enctype para subir archivos -->
                    <form id="form-registrar-bien" method="POST" action="{{ route("$prefix.bienes.store") }}"
                        enctype="multipart/form-data">
                        @csrf <!-- Token de seguridad de Laravel -->

                        <div class="row">
                            <!-- Formulario principal -->
                            <div class="col-md-8 col-12">
                                <!-- Campos del formulario -->
                                <div class="mb-3">
                                    <label for="numeroInventario" class="form-label fw-bold">N° de inventario</label>
                                    <input type="text"
                                        class="form-control @error('numero_inventario') is-invalid @enderror"
                                        id="numeroInventario" name="numero_inventario"
                                        placeholder="Ingrese el número de inventario" value="{{ old('numero_inventario') }}"
                                        required>
                                    @error('numero_inventario')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="numeroSerie" class="form-label fw-bold">N° de serie</label>
                                    <input type="text" class="form-control" id="numeroSerie" name="numero_serie"
                                        placeholder="Ingrese el número de serie" value="{{ old('numero_serie') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="nombre" class="form-label fw-bold">Nombre</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                        id="nombre" name="nombre" placeholder="Ingrese el nombre del bien"
                                        value="{{ old('nombre') }}" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="descripcionGeneral" class="form-label fw-bold">Descripción general</label>
                                    <textarea class="form-control" id="descripcionGeneral" name="descripcion_general"
                                        placeholder="Ingrese la descripción del bien">{{ old('descripcion_general') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="observaciones" class="form-label fw-bold">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" placeholder="Ingrese las observaciones del bien">{{ old('observaciones') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="fechaAdquisicion" class="form-label fw-bold">Fecha de adquisición</label>
                                    <input type="date"
                                        class="form-control @error('fecha_adquisicion') is-invalid @enderror"
                                        id="fechaAdquisicion" name="fecha_adquisicion"
                                        value="{{ old('fecha_adquisicion') }}" required>
                                    @error('fecha_adquisicion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="costo" class="form-label fw-bold">Costo (MXN)</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('costo') is-invalid @enderror" id="costo"
                                        name="costo" placeholder="Ingrese el costo del bien" value="{{ old('costo') }}"
                                        required>
                                    @error('costo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="departamento" class="form-label fw-bold">Departamento</label>
                                    <select class="form-select @error('id_departamento') is-invalid @enderror"
                                        id="departamento" name="id_departamento" required>
                                        <option selected disabled>Seleccione el departamento</option>
                                        @foreach ($departamentos as $departamento)
                                            <option value="{{ $departamento->id_departamento }}"
                                                {{ old('id_departamento') == $departamento->id_departamento ? 'selected' : '' }}>
                                                {{ $departamento->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_departamento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="categoria" class="form-label fw-bold">Categoría</label>
                                    <select class="form-select @error('categoria') is-invalid @enderror" id="categoria"
                                        name="categoria" required>
                                        <option selected disabled>Seleccionar categoría</option>
                                        <option value="Equipo tecnológico"
                                            {{ old('categoria') == 'Equipo tecnológico' ? 'selected' : '' }}>Equipo
                                            tecnológico</option>
                                        <option value="Mobiliario"
                                            {{ old('categoria') == 'Mobiliario' ? 'selected' : '' }}>Mobiliario</option>
                                    </select>
                                    @error('categoria')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Sección lateral -->
                            <div class="col-md-4 col-12 mt-4 mt-md-0">
                                <div class="card p-3 mb-3">
                                    <h5>Estado del bien</h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="estado" id="activo"
                                            value="activo" checked>
                                        <label class="form-check-label" for="activo">Activo</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="estado" id="inactivo"
                                            value="inactivo">
                                        <label class="form-check-label" for="inactivo">Inactivo</label>
                                    </div>
                                </div>

                                <div class="card p-3">
                                    <h5>Imagen</h5>
                                    <input type="file" class="form-control @error('imagen') is-invalid @enderror"
                                        id="subirImagen" name="imagen" accept="image/*">
                                    <img id="imagenPreview" class="img-fluid mt-2"
                                        style="max-height: 150px; display: none;">
                                    @error('imagen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success mx-2">Guardar</button>
                            <a href="{{ route("$prefix.bienes.index") }}" class="btn btn-danger mx-2">Atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 y Previsualización de Imagen -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('subirImagen').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagenPreview').src = e.target.result;
                    document.getElementById('imagenPreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('form-registrar-bien').addEventListener('submit', function(event) {
            event.preventDefault();
            let costo = document.getElementById('costo').value;

            // Validación del costo: solo números y dentro del rango permitido
            if (!/^\d+(\.\d{1,2})?$/.test(costo) || parseFloat(costo) > 99999999.99 || parseFloat(costo) < 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en el costo',
                    text: 'El costo debe ser un número válido entre 0 y 99,999,999.99 MXN.',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            // Confirmación de guardado solo si el costo es válido
            Swal.fire({
                title: '¿Confirmar registro?',
                text: 'Se guardará el bien en el sistema.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-registrar-bien').submit();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            function capitalizeFirstLetter(input) {
                input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);
            }

            // Selecciona los campos que quieres capitalizar automáticamente
            const fieldsToCapitalize = ['nombre', 'descripcionGeneral', 'observaciones'];

            fieldsToCapitalize.forEach(fieldId => {
                const input = document.getElementById(fieldId);
                if (input) {
                    input.addEventListener('input', function() {
                        capitalizeFirstLetter(input);
                    });
                }
            });
        });
    </script>

@endsection
