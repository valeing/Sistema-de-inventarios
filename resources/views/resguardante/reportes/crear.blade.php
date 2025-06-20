@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Reportar un Bien</h2>
        @isset($mensajeError)
            <div class="alert alert-warning text-center fw-semibold" role="alert">
                {{ $mensajeError }}
            </div>
        @else

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('resguardante.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reportar bien</li>
            </ol>
        </nav>
            <div class="card p-4 shadow-sm">
                <form action="{{ route('reportes.store') }}" method="POST">
                    @csrf

                    <!-- Bien seleccionado -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bien seleccionado</label>
                        <div class="input-group">
                            <input type="text" id="bienSeleccionado" class="form-control" placeholder="Selecciona un bien..."
                                readonly>
                            <button type="button" class="btn btn-primary" id="btnBuscarBien" title="Abrir selector de bienes">
                                Buscar bien
                            </button>
                        </div>
                        <input type="hidden" name="bien_id" id="id_bien" required>
                    </div>

                    <!-- Comentario -->
                    <div class="mb-3">
                        <label for="comentario" class="form-label fw-bold">Comentario</label>
                        <textarea name="comentario" class="form-control" rows="4" placeholder="Describe el problema del bien..." required></textarea>
                    </div>

                    <!-- Botón -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Enviar Reporte</button>
                    </div>
                </form>
            </div>
        @endisset
    </div>

    {{-- Modal personalizado --}}
    <div class="modal fade" id="modalBienes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 1000px;">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white d-flex justify-content-between">
                    <h5 class="modal-title">Bienes disponibles</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="searchBienes" class="form-control mb-3"
                        placeholder="Buscar por N.º Inventario o Nombre...">
                    <div class="table-responsive">
                        <table id="tablaBienes" class="table table-bordered table-striped w-100">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th class="no-sort">N.º INVENTARIO</th>
                                    <th class="no-sort">NOMBRE</th>
                                    <th class="no-sort">ESTADO</th>
                                    <th class="no-sort">IMAGEN</th>
                                    <th class="no-sort">SELECCIONAR</th>
                                </tr>
                            </thead>
                            <tbody class="text-center"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Estilos personalizados --}}
    <style>
        .dataTables_length,
        .dataTables_filter {
            display: none !important;
        }

        table.dataTable thead .sorting::after,
        table.dataTable thead .sorting_asc::after,
        table.dataTable thead .sorting_desc::after {
            display: none !important;
        }

        .modal-dialog {
            max-width: 1000px;
        }

        #tablaBienes th,
        #tablaBienes td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 10px;
            vertical-align: middle;
        }

        #tablaBienes img {
            width: 55px;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .btn-close {
            background-color: white;
        }
    </style>

    <script>
        let tablaBienes;
        let modalBienes;

        document.addEventListener("DOMContentLoaded", function() {
            modalBienes = new bootstrap.Modal(document.getElementById('modalBienes'));

            document.getElementById("btnBuscarBien").addEventListener("click", function() {
                modalBienes.show();

                if (!tablaBienes) {
                    tablaBienes = $('#tablaBienes').DataTable({
                        processing: true,
                        serverSide: true,
                        paging: true,
                        ordering: false,
                        info: false,
                        lengthChange: false,
                        searching: false,
                        pageLength: 10,
                        ajax: {
                            url: '/resguardante/bienes/buscar',
                            type: 'GET',
                            data: function(d) {
                                d.search = $('#searchBienes').val();
                            },
                            error: function(xhr) {
                                console.error("Error al cargar los bienes:", xhr.responseText);
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
                                data: 'estado',
                                className: 'text-center'
                            },
                            {
                                data: 'imagen_url',
                                className: 'text-center',
                                render: function(data) {
                                    return `<img src="${data}" alt="imagen del bien" width="50" class="rounded border">`;
                                }
                            },
                            {
                                data: null,
                                className: 'text-center',
                                render: function(data) {
                                    return `<button type="button" class="btn btn-success btn-sm seleccionar-bien"
                                    title="Seleccionar bien"
                                    data-id="${data.id_bien}"
                                    data-nombre="${data.nombre}"
                                    data-inventario="${data.numero_inventario}">
                                    Seleccionar
                                </button>`;
                                }
                            }
                        ],
                        language: {
                            paginate: {
                                previous: "«",
                                next: "»"
                            },
                            zeroRecords: "No se encontraron bienes.",
                            loadingRecords: "Cargando...",
                            processing: "Procesando..."
                        },
                        drawCallback: function() {
                            estilizarPaginacion();
                        }
                    });

                    // Buscar en tiempo real
                    $('#searchBienes').on('input', function() {
                        tablaBienes.ajax.reload();
                    });
                } else {
                    tablaBienes.ajax.reload();
                }
            });

            // Seleccionar bien y cerrar modal
            $(document).on('click', '.seleccionar-bien', function() {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');
                const inventario = $(this).data('inventario');

                $('#bienSeleccionado').val(`${nombre} (${inventario})`);
                $('#id_bien').val(id);
                modalBienes.hide();
            });

            // Personalizar paginación
            function estilizarPaginacion() {
                let pagination = $('.dataTables_paginate');
                pagination.addClass('pagination justify-content-center mt-3');

                pagination.find('a').each(function() {
                    $(this).addClass('page-link');
                });

                pagination.find('li').each(function() {
                    $(this).addClass('page-item');
                });

                pagination.find('.current').parent().addClass('active');
            }
        });
    </script>
@endsection
