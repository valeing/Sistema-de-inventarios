@extends('layouts.app')

@section('title', 'Editar bien')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h2>Editar bien</h2>

                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route("$prefix.bienes.index") }}">Bienes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Editar bien</li>
                    </ol>
                </nav>

                <!-- Contenedor principal -->
                <div class="card p-4">
                    <form id="form-editar-bien" method="POST" action="{{ route("$prefix.bienes.update", $bien->id_bien) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Campos principales -->
                            <div class="col-md-8 col-12">
                                <div class="mb-3">
                                    <label for="numeroInventario" class="form-label">N° de inventario</label>
                                    <input type="text"
                                        class="form-control @error('numero_inventario') is-invalid @enderror"
                                        id="numeroInventario" name="numero_inventario"
                                        value="{{ old('numero_inventario', $bien->numero_inventario) }}" required>
                                    @error('numero_inventario')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="numeroSerie" class="form-label">N° de serie</label>
                                    <input type="text" class="form-control @error('numero_serie') is-invalid @enderror"
                                        id="numeroSerie" name="numero_serie"
                                        value="{{ old('numero_serie', $bien->numero_serie) }}">
                                    @error('numero_serie')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                        id="nombre" name="nombre" value="{{ old('nombre', $bien->nombre) }}" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="descripcionGeneral" class="form-label">Descripción general</label>
                                    <textarea class="form-control @error('descripcion_general') is-invalid @enderror" id="descripcionGeneral"
                                        name="descripcion_general">{{ old('descripcion_general', $bien->descripcion_general) }}</textarea>
                                    @error('descripcion_general')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones">{{ old('observaciones', $bien->observaciones) }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="costo" class="form-label">Costo (MXN)</label>
                                    <input type="text" class="form-control @error('costo') is-invalid @enderror"
                                        id="costo" name="costo"
                                        value="{{ old('costo', number_format($bien->costo, 2, '.', '')) }}" required>
                                    @error('costo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="fechaAdquisicion" class="form-label">Fecha de adquisición del bien</label>
                                    <input type="date"
                                        class="form-control @error('fecha_adquisicion') is-invalid @enderror"
                                        id="fechaAdquisicion" name="fecha_adquisicion"
                                        value="{{ old('fecha_adquisicion', $bien->fecha_adquisicion) }}" required>
                                    @error('fecha_adquisicion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="departamento" class="form-label">Departamento</label>
                                    <select class="form-select @error('id_departamento') is-invalid @enderror"
                                        id="departamento" name="id_departamento" required>
                                        <option selected disabled>Seleccione el departamento</option>
                                        @foreach ($departamentos as $departamento)
                                            <option value="{{ $departamento->id_departamento }}"
                                                {{ old('id_departamento', $bien->id_departamento) == $departamento->id_departamento ? 'selected' : '' }}>
                                                {{ $departamento->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_departamento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="categoria" class="form-label">Categoría</label>
                                    <select class="form-select @error('categoria') is-invalid @enderror" id="categoria"
                                        name="categoria" required>
                                        <option selected disabled>Seleccionar categoría</option>
                                        <option value="Equipo tecnológico"
                                            {{ old('categoria', $bien->categoria) == 'Equipo tecnológico' ? 'selected' : '' }}>
                                            Equipo tecnológico</option>
                                        <option value="Mobiliario"
                                            {{ old('categoria', $bien->categoria) == 'Mobiliario' ? 'selected' : '' }}>
                                            Mobiliario</option>
                                    </select>
                                    @error('categoria')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Lateral -->
                            <div class="col-md-4 col-12 mt-4 mt-md-0">
                                <div class="card mb-3 p-3">
                                    <h5>Estado del bien</h5>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="estado" id="activo"
                                            value="activo"
                                            {{ old('estado', $bien->estado) == 'activo' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="activo">Activo</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="estado" id="inactivo"
                                            value="inactivo"
                                            {{ old('estado', $bien->estado) == 'inactivo' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="inactivo">Inactivo</label>
                                    </div>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="card p-3">
                                    <h5>Imagen actual</h5>
                                    @if ($bien->imagen)
                                        <img src="data:{{ $bien->mime_type }};base64,{{ $bien->imagen }}"
                                            alt="Imagen actual" class="img-fluid">
                                    @else
                                        <p>No hay imagen disponible</p>
                                    @endif
                                    <p>Actualizar imagen:</p>
                                    <input type="file" class="form-control @error('imagen') is-invalid @enderror"
                                        id="subirImagen" name="imagen">
                                    @error('imagen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary mx-2">Guardar</button>
                            <a href="{{ route("$prefix.bienes.index") }}" class="btn btn-danger mx-2">Atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function capitalizeFirstLetter(input) {
                input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);
            }

            // Capitalizar automáticamente ciertos campos
            const fieldsToCapitalize = ['nombre', 'descripcionGeneral', 'observaciones'];
            fieldsToCapitalize.forEach(fieldId => {
                const input = document.getElementById(fieldId);
                if (input) {
                    input.addEventListener('input', function() {
                        capitalizeFirstLetter(input);
                    });
                }
            });

            // Validar y mostrar mensaje de error con SweetAlert2
            document.getElementById('form-editar-bien').addEventListener('submit', function(event) {
                let costoInput = document.getElementById('costo').value.trim();
                let costo = parseFloat(costoInput.replace(/,/g, ''));

                if (isNaN(costo) || costo < 0 || costo > 99999999.99) {
                    event.preventDefault(); // Solo evita el envío si el costo es inválido

                    Swal.fire({
                        icon: 'error',
                        title: 'Error en el costo',
                        text: 'El costo debe ser un número válido entre 0 y 99,999,999.99 MXN.',
                        confirmButtonColor: '#d33'
                    });
                }
            });

            // Detectar errores pasados desde la función y mostrarlos con SweetAlert2
            @if ($errors->any())
                let errorMessages = "";
                @foreach ($errors->all() as $error)
                    errorMessages += "{{ $error }}" + "\n";
                @endforeach

                Swal.fire({
                    icon: 'error',
                    title: 'Errores en el formulario',
                    text: errorMessages,
                    confirmButtonColor: '#d33'
                });
            @endif
        });
    </script>

@endsection
