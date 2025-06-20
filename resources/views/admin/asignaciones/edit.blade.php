@extends('layouts.app')

@section('title', 'Editar asignación')

@section('content')
    <div class="container mt-5">
        <h2>Editar Asignación</h2>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.asignaciones.index") }}">Asignaciones</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route("$prefix.asignaciones.show", $asignacion->id_resguardante) }}">Bienes asignados</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Editar bienes</li>
            </ol>
        </nav>

        <div class="card p-4 shadow-sm">
            <!-- Formulario para editar asignación -->
            <form action="{{ route("$prefix.asignaciones.update", $asignacion->id_asignacion) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Sección de Bienes -->
                <div class="card p-4 mb-4">
                    <h5>Bien asignado</h5>
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                        data-bs-target="#modalBienes">
                        Seleccionar Bien
                    </button>

                    <div class="mb-3">
                        <strong>Bien asignado:</strong>
                        <div class="form-control bg-light d-flex align-items-center p-2" style="min-height: 60px;">
                            <div class="me-3 d-flex align-items-center justify-content-center"
                                style="width: 55px; height: 55px;">
                                <img id="imagenBien"
                                    src="{{ $asignacion->bien->imagen ? 'data:' . $asignacion->bien->mime_type . ';base64,' . $asignacion->bien->imagen : 'https://via.placeholder.com/50' }}"
                                    alt="Imagen del bien" class="rounded border"
                                    style="width: 50px; height: 50px; object-fit: cover;">
                            </div>
                            <span id="bienSeleccionado">
                                {{ $asignacion->bien->nombre }} (N° de Inventario:
                                {{ $asignacion->bien->numero_inventario }})
                            </span>
                        </div>
                        <input type="hidden" name="id_bien" id="id_bien" value="{{ $asignacion->id_bien }}" required>
                    </div>
                </div>

                <!-- Sección de Resguardantes -->
                <div class="card p-4">
                    <h5>Resguardante</h5>
                    <div class="mb-3">
                        <label for="buscarResguardante" class="form-label">Buscar resguardante por nombre o número de
                            empleado</label>
                        <input type="text" class="form-control" id="buscarResguardante"
                            placeholder="Escribe para buscar..."
                            value="{{ $asignacion->resguardante->nombre_apellido }} (N° de personal: {{ $asignacion->resguardante->numero_empleado }})">

                        <!-- 🔹 Se agregó este div para mostrar los resultados dinámicos -->
                        <div id="resguardanteResultados" class="list-group mt-2"
                            style="max-height: 200px; overflow-y: auto;"></div>

                        <input type="hidden" name="id_resguardante" id="id_resguardante"
                            value="{{ $asignacion->id_resguardante }}" required>

                        <div id="mensajeResguardante" class="text-danger mt-1"></div>
                    </div>


                    <div class="mb-3">
                        <label for="fecha_asignacion" class="form-label">Fecha de asignación</label>
                        <input type="date" class="form-control" name="fecha_asignacion" id="fecha_asignacion"
                            value="{{ $asignacion->fecha_asignacion }}">
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary mx-2">Guardar cambios</button>
                    <a href="{{ route("$prefix.asignaciones.index") }}" class="btn btn-danger mx-2">Cancelar</a>
                </div>
            </form>

        </div>
    </div>

    @include('componentes.modal_bienes1')

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let table = $('#tablaBienes').DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                searching: false,
                info: false,
                lengthChange: false,
                ordering: false,
                language: {
                    paginate: {
                        previous: "«",
                        next: "»"
                    },
                    zeroRecords: "No se encontraron bienes.",
                },
                ajax: {
                    url: '/administrador/bienes/buscar',
                    type: 'GET',
                    data: function(d) {
                        d.search = $('#searchBienes').val();
                        d.categoria = $('#categoriaFiltro').val();
                    },
                    error: function(xhr) {
                        console.error("Error cargando bienes:", xhr.responseText);
                    }
                },
                columns: [{
                        data: 'numero_inventario',
                        className: 'text-center'
                    },
                    {
                        data: 'nombre',
                        className: 'text-center'
                    },
                    {
                        data: 'observaciones',
                        className: 'text-center'
                    },
                    {
                        data: 'fecha_adquisicion',
                        className: 'text-center'
                    },
                    {
                        data: 'imagen_url',
                        className: 'text-center',
                        render: function(data) {
                            return `<img src="${data}" alt="Imagen del bien">`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<button class="btn btn-success btn-sm seleccionar-bien"
                                        data-id="${row.id_bien}"
                                        data-nombre="${row.nombre}"
                                        data-inventario="${row.numero_inventario}"
                                        data-imagen="${row.imagen_url}">
                                        <i class="bi bi-check-circle"></i> Seleccionar
                                    </button>`;
                        }
                    }
                ],
                drawCallback: function() {
                    estilizarPaginacion();
                }
            });

            $('#searchBienes').on('keyup', function() {
                table.ajax.reload();
            });

            $('#categoriaFiltro').on('change', function() {
                table.ajax.reload();
            });

            $(document).on('click', '.seleccionar-bien', function() {
                let id = $(this).data('id');
                let nombre = $(this).data('nombre');
                let inventario = $(this).data('inventario');
                let imagen = $(this).data('imagen');

                document.getElementById('bienSeleccionado').innerHTML = `
                    <img src="${imagen}" alt="Imagen del bien" class="me-2 rounded border"
                        style="width: 50px; height: 50px; object-fit: cover;">
                    ${nombre} (N° Inventario: ${inventario})`;

                document.getElementById('id_bien').value = id;
                cerrarModal();
            });

            function cerrarModal() {
                $('#modalBienes').modal('hide');
            }

            window.mostrarModal = function() {
                $('#modalBienes').modal('show');
                table.ajax.reload();
            };

            function estilizarPaginacion() {
                let pagination = $('.dataTables_paginate');
                pagination.addClass('pagination justify-content-center mt-3');
                pagination.find('a').addClass('page-link');
                pagination.find('li').addClass('page-item');
                pagination.find('.current').parent().addClass('active');
            }

            window.guardarSeleccion = function() {
                $('#modalBienes').modal('hide');
            };
        });
    </script>


    <script src="{{ asset('js/buscar-resguardante.js') }}"></script>








@endsection
