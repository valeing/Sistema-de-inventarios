<!-- Modal para seleccionar bien -->
<div class="modal fade" id="modalSeleccionarBien" tabindex="-1" aria-labelledby="modalSeleccionarBienLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Cambiamos el tamaño del modal a extra grande -->
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-box-seam"></i> Seleccionar Bien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <!-- Buscador -->
                <div class="mb-3">
                    <input type="text" id="buscarBien" class="form-control"
                        placeholder="Buscar bien por número o nombre...">
                </div>

                <!-- Tabla con bienes inactivos -->
                <div class="table-responsive">
                    <table id="tablaBienes" class="table table-striped table-hover table-bordered align-middle">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th class="col-3">N° INVENTARIO</th>
                                <th class="col-5">NOMBRE</th>
                                <th class="col-2">ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody id="bienesResultados">
                            <!-- Se llenará con AJAX -->
                        </tbody>
                    </table>
                </div>

                <!-- Paginación con Bootstrap -->
                <nav id="paginacionBienes" class="mt-3 d-flex justify-content-center">
                    <!-- Se generará dinámicamente -->
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let urlBase = "/admin/bajas/listar-bienes"; // ✅ Ruta corregida

        let table = $("#tablaBienes").DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            searching: false,
            info: false,
            lengthChange: false,
            ordering: false,
            pageLength: 10,
            autoWidth: false, // ❌ Evita que las columnas sean demasiado grandes
            responsive: true, // ✅ Hace la tabla adaptable
            language: {
                paginate: {
                    previous: "«",
                    next: "»"
                },
                zeroRecords: "No se encontraron bienes inactivos.",
                loadingRecords: "Cargando bienes...",
                processing: "Procesando...",
            },
            ajax: {
                url: urlBase,
                type: "GET",
                data: function(d) {
                    d.search = $("#buscarBien").val();
                },
                error: function(xhr) {
                    console.error("Error en la petición AJAX:", xhr.responseText);
                }
            },
            columns: [{
                    data: "numero_inventario",
                    className: "text-center",
                    width: "30%"
                },
                {
                    data: "nombre",
                    className: "text-center",
                    width: "50%"
                },
                {
                    data: null,
                    className: "text-center",
                    width: "20%",
                    render: function(data, type, row) {
                        return `<button class="btn btn-success seleccionarBien"
                            data-id="${row.id_bien}" data-inventario="${row.numero_inventario}" data-nombre="${row.nombre}">
                            <i class="bi bi-check-circle"></i> Seleccionar
                        </button>`;
                    }
                }
            ],
            drawCallback: function() {
                estilizarPaginacion();
            }
        });

        // ✅ Filtrar cuando el usuario escriba en el campo de búsqueda
        $("#buscarBien").on("keyup", function() {
            table.ajax.reload();
        });

        // ✅ Evento para seleccionar un bien y evitar validación del formulario
        $(document).on("click", ".seleccionarBien", function(event) {
            event.preventDefault(); // ❌ Evita que el formulario se valide

            let idBien = $(this).data("id");
            let numeroInventario = $(this).data("inventario");
            let nombreBien = $(this).data("nombre");

            $("#id_bien").val(idBien);
            $("#bienSeleccionado").val(`${numeroInventario} - ${nombreBien}`);

            // 🔹 Deshabilita validaciones al seleccionar un bien
            $("#formBaja").attr("novalidate", "true");

            $("#modalSeleccionarBien").modal("hide");
        });

        // ✅ Recargar la tabla cuando se abre el modal
        $("#modalSeleccionarBien").on("shown.bs.modal", function() {
            table.ajax.reload();
        });

        function estilizarPaginacion() {
            let pagination = $(".dataTables_paginate");
            pagination.addClass("pagination justify-content-center mt-3");

            pagination.find("a").each(function() {
                $(this).addClass("page-link");
            });

            pagination.find("li").each(function() {
                $(this).addClass("page-item");
            });

            pagination.find(".current").parent().addClass("active");
        }
    });
</script>
