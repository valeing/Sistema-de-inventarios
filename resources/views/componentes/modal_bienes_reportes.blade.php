@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Reportar un Bien</h2>

        <div class="card p-4 shadow-sm">
            <form action="{{ route('reportes.store') }}" method="POST">
                @csrf

                <!-- Bien seleccionado -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Bien seleccionado</label>
                    <div class="input-group">
                        <input type="text" id="bienSeleccionado" class="form-control" placeholder="Selecciona un bien..." readonly>
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
    </div>

    <!-- Modal personalizado -->
    <div class="modal fade" id="modalBienes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">

                <!-- ENCABEZADO -->
                <div class="modal-header bg-primary text-white py-2 px-3">
                    <h5 class="modal-title">Bienes disponibles</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- CUERPO -->
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchBienes" class="form-control" placeholder="Buscar por N.º Inventario o Nombre...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="tablaBienes" class="table table-bordered text-center align-middle w-100">
                            <thead class="table-light" style="background-color: #e0e7ff;">
                                <tr class="no-sort">
                                    <th>Inventario</th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <th>Imagen</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        /* Estilo personalizado */
        .modal-dialog {
            max-width: 1000px;
        }

        table.dataTable thead .sorting::after,
        table.dataTable thead .sorting_asc::after,
        table.dataTable thead .sorting_desc::after,
        .dataTables_length,
        .dataTables_filter {
            display: none !important;
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

        document.addEventListener("DOMContentLoaded", function () {
            const modalElement = document.getElementById('modalBienes');
            modalBienes = new bootstrap.Modal(modalElement);

            document.getElementById("btnBuscarBien").addEventListener("click", function () {
                modalBienes.show();

                if (!tablaBienes) {
                    tablaBienes = $('#tablaBienes').DataTable({
                        processing: true,
                        serverSide: false,
                        ordering: false,
                        paging: true,
                        info: false,
                        ajax: function (data, callback) {
                            $.ajax({
                                url: '/resguardante/bienes/buscar',
                                type: 'GET',
                                data: {
                                    search: $('#searchBienes').val()
                                },
                                success: function (response) {
                                    callback({ data: response.data });
                                },
                                error: function () {
                                    alert('Error al cargar los bienes');
                                }
                            });
                        },
                        columns: [
                            { data: 'numero_inventario' },
                            { data: 'nombre' },
                            { data: 'estado' },
                            {
                                data: 'imagen_url',
                                render: function (data) {
                                    return `<img src="${data}" alt="imagen del bien">`;
                                }
                            },
                            {
                                data: null,
                                render: function (data) {
                                    return `<button type="button" class="btn btn-success btn-sm seleccionar-bien"
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
                            zeroRecords: "No se encontraron bienes."
                        }
                    });

                    $('#searchBienes').on('keyup', function () {
                        tablaBienes.ajax.reload();
                    });
                } else {
                    tablaBienes.ajax.reload();
                }
            });

            // Selección de bien
            $(document).on('click', '.seleccionar-bien', function () {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');
                const inventario = $(this).data('inventario');

                $('#bienSeleccionado').val(`${nombre} (${inventario})`);
                $('#id_bien').val(id);

                modalBienes.hide();
            });
        });
    </script>
@endsection
