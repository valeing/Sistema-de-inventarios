@extends('layouts.app')


@section('title', 'Agregar Dirección y Departamentos')

@section('content')
    <div class="container mt-5">
        <h2>Agregar Dirección y Departamentos</h2>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.direct.index") }}">Direcciones y Departamentos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registrar dirección y departamentos</li>
            </ol>
        </nav>

        <div class="card shadow-sm p-4">
            {{-- Alertas de éxito o error --}}
            @if (session('success'))
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: @json(session('success')),
                            confirmButtonColor: '#28a745',
                        });
                    });
                </script>
            @endif

            @if ($errors->any())
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: @json(implode('<br>', $errors->all())),
                            confirmButtonColor: '#d33',
                        });
                    });
                </script>
            @endif

            <form id="create-form" action="{{ route("$prefix.direct.store") }}" method="POST">
                @csrf

                {{-- Campo para Nombre de la Dirección --}}
                <div class="mb-3">
                    <label for="nombre_direccion" class="form-label fw-bold">Nombre de la Dirección</label>
                    <input type="text" class="form-control" id="nombre_direccion" name="nombre_direccion" required>
                    <div class="form-text">Asegúrate de ingresar un nombre adecuado para la dirección.</div>
                </div>

                {{-- Lista de Departamentos --}}
                <label class="form-label fw-bold">Departamentos Asociados</label>
                <div id="departamentos-container" class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                    <div class="input-group mb-2 department-group">
                        <input type="text" class="form-control department-input" name="nombre_departamentos[]" placeholder="Nombre del departamento" required>
                        <button type="button" class="btn btn-outline-danger remove-department d-none">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </div>
                </div>

                {{-- Botón para agregar más departamentos --}}
                <button type="button" id="add-department-btn" class="btn btn-primary mt-3">
                    <i class="fas fa-plus"></i> Agregar Departamento
                </button>

                {{-- Botones de Acción --}}
                <div class="d-flex justify-content-center mt-4">
                    <button type="button" id="btn-confirmar" class="btn btn-success mx-2">
                         Guardar Dirección
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

    {{-- Script para Confirmación y Manejo de Departamentos --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addDepartmentBtn = document.getElementById('add-department-btn');
            const departamentosContainer = document.getElementById('departamentos-container');
            const direccionInput = document.getElementById('nombre_direccion');
            const form = document.getElementById('create-form');
            const btnConfirmar = document.getElementById('btn-confirmar');

            // Mostrar confirmación antes de enviar el formulario
            btnConfirmar.addEventListener('click', function() {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Se guardará la nueva dirección y sus departamentos asociados.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Envía el formulario solo si el usuario confirma
                    }
                });
            });

            // Capitalizar la primera letra automáticamente
            function capitalizeFirstLetter(input) {
                input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1).toLowerCase();
            }

            direccionInput.addEventListener('input', function() {
                capitalizeFirstLetter(direccionInput);
            });

            // Agregar nuevos departamentos dinámicamente
            addDepartmentBtn.addEventListener('click', function() {
                if (departamentosContainer.childElementCount >= 6) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Límite alcanzado',
                        text: 'No puedes agregar más de 6 departamentos.',
                        confirmButtonColor: '#3085d6',
                    });
                    return;
                }

                const departmentGroup = document.createElement('div');
                departmentGroup.classList.add('input-group', 'mb-2', 'department-group');

                const input = document.createElement('input');
                input.type = 'text';
                input.classList.add('form-control', 'department-input');
                input.name = 'nombre_departamentos[]';
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
                        showDeleteConfirmation(departmentGroup);
                    }
                });

                departmentGroup.appendChild(input);
                departmentGroup.appendChild(removeBtn);
                departamentosContainer.appendChild(departmentGroup);
            });

            // Confirmación antes de eliminar departamentos existentes
            departamentosContainer.addEventListener('click', function(event) {
                const btn = event.target.closest('.remove-department');
                if (btn) {
                    const departmentGroup = btn.closest('.department-group');
                    const input = departmentGroup.querySelector('.department-input');

                    if (input.value.trim() === "") {
                        departmentGroup.remove();
                    } else {
                        showDeleteConfirmation(departmentGroup);
                    }
                }
            });

            // Función para mostrar confirmación antes de eliminar
            function showDeleteConfirmation(departmentGroup) {
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
                        departmentGroup.remove();
                    }
                });
            }
        });
    </script>
@endsection
