@extends('layouts.app')


@section('title', 'Editar Dirección y Departamentos')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center mb-4">Editar Dirección y Departamentos</h2>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.direct.index") }}">Direcciones y Departamentos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Dirección</li>
            </ol>
        </nav>

        <div class="card shadow-sm p-4">
            {{-- Alertas de éxito o error --}}
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

            <form id="edit-form" action="{{ route("$prefix.direct.update", $direccion->id_direccion) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Campo para Nombre de la Dirección --}}
                <div class="mb-3">
                    <label for="nombre_direccion" class="form-label fw-bold">Nombre de la Dirección</label>
                    <input type="text" class="form-control" id="nombre_direccion" name="nombre_direccion"
                        value="{{ $direccion->nombre }}" required>
                    <div class="form-text">Asegúrate de ingresar un nombre adecuado para la dirección.</div>
                </div>

                {{-- Lista de Departamentos --}}
                <label class="form-label fw-bold">Departamentos Asociados</label>
                <div id="departamentos-container" class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                    @foreach ($direccion->departamentos as $departamento)
                        <div class="input-group mb-2 department-group" data-id="{{ $departamento->id_departamento }}">
                            <input type="text" class="form-control department-input"
                                name="departamentos_existentes[{{ $departamento->id_departamento }}]"
                                value="{{ $departamento->nombre }}" required>
                            <button type="button" class="btn btn-outline-danger remove-department"
                                data-id="{{ $departamento->id_departamento }}">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- Botón para agregar más departamentos --}}
                <button type="button" id="add-department-btn" class="btn btn-primary mt-3">
                    <i class="fas fa-plus"></i> Agregar Departamento
                </button>

                {{-- Botones de Acción --}}
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-success mx-2">
                        Guardar Cambios
                    </button>
                    <a href="{{ route("$prefix.direct.index") }}" class="btn btn-danger mx-2">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Cargar SweetAlert2 desde CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Script para Interactividad --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addDepartmentBtn = document.getElementById('add-department-btn');
            const departamentosContainer = document.getElementById('departamentos-container');

            // Agregar nuevos departamentos dinámicamente
            addDepartmentBtn.addEventListener('click', function() {
                const departmentGroup = document.createElement('div');
                departmentGroup.classList.add('input-group', 'mb-2', 'department-group');

                const input = document.createElement('input');
                input.type = 'text';
                input.classList.add('form-control', 'department-input');
                input.name = 'departamentos_nuevos[]';
                input.placeholder = 'Nombre del departamento';
                input.required = true;

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.classList.add('btn', 'btn-outline-danger', 'remove-department');
                removeBtn.innerHTML = '<i class="fas fa-trash-alt"></i> Eliminar';

                removeBtn.addEventListener('click', function() {
                    if (input.value.trim() === "") {
                        departmentGroup.remove();
                    } else {
                        showDeleteConfirmation(departmentGroup, null);
                    }
                });

                departmentGroup.appendChild(input);
                departmentGroup.appendChild(removeBtn);
                departamentosContainer.appendChild(departmentGroup);
            });

            // Eliminar departamentos existentes con lógica mejorada
            departamentosContainer.addEventListener('click', function(event) {
                const btn = event.target.closest('.remove-department');
                if (btn) {
                    const departmentGroup = btn.closest('.department-group');
                    const departmentId = btn.getAttribute('data-id');
                    const input = departmentGroup.querySelector('.department-input');

                    if (input.value.trim() === "") {
                        departmentGroup.remove();
                    } else {
                        showDeleteConfirmation(departmentGroup, departmentId);
                    }
                }
            });

            // Función para mostrar confirmación antes de eliminar
            function showDeleteConfirmation(departmentGroup, departmentId) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'No podrás recuperar este departamento si lo eliminas.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (departmentId) {
                            fetch(`/admin/direct/departamento/${departmentId}/eliminar`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        departmentGroup.remove();
                                    } else {
                                        Swal.fire('Error', data.message, 'error');
                                    }
                                });
                        } else {
                            departmentGroup.remove();
                        }
                    }
                });
            }
        });
    </script>
@endsection
