<style>
    /* Estilo del encabezado */
    .table thead {
        background-color: #0d6efd !important;
        color: white !important;
        text-align: center;
    }

    /* Estilo general de la tabla */
    .table {
        table-layout: fixed;
        width: 100%;
    }

    .table th,
    .table td {
        text-align: center;
        vertical-align: middle;
        padding: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Control de ancho por columna */
    #tablaBienes th:nth-child(1) {
        width: 18%;
    }

    #tablaBienes th:nth-child(2) {
        width: 18%;
    }

    #tablaBienes th:nth-child(3) {
        width: 17%;
    }

    #tablaBienes th:nth-child(4) {
        width: 14%;
    }

    #tablaBienes th:nth-child(5) {
        width: 13%;
    }

    #tablaBienes th:nth-child(6) {
        width: 10%;
    }

    /* Centrar completamente el checkbox individual */
    .checkbox-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    /* Escala visual para el checkbox sin deformarlo */
    .bien-checkbox {
        transform: scale(1.2);
        margin: 0;
    }

    /* Centrar la celda de selección */
    #tablaBienes th:last-child,
    #tablaBienes td:last-child {
        text-align: center !important;
        vertical-align: middle !important;
        padding-top: 0;
        padding-bottom: 0;
    }

    /* Contenedor para el checkbox de seleccionar todos */
    .select-all-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        line-height: 1.2;
    }

    /* Alternancia de color */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(13, 110, 253, 0.05);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.1);
    }

    /* Escala base del checkbox de Bootstrap */
    .form-check-input {
        transform: scale(1.2);
    }

    /* Paginación personalizada */
    .dataTables_paginate {
        display: flex;
        justify-content: center;
    }

    .dataTables_paginate a {
        padding: 5px 10px;
        margin: 2px;
        border-radius: 5px;
        border: 1px solid #0d6efd;
        color: #0d6efd;
        text-decoration: none;
    }

    .dataTables_paginate a:hover {
        background-color: #0d6efd;
        color: white;
    }

    /* Evitar scroll horizontal en el modal */
    .modal-body {
        overflow-x: hidden;
    }
</style>

<!-- Modal -->
<div id="modalBienes" class="modal fade" tabindex="-1" aria-labelledby="modalBienesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-box-seam"></i> Bienes disponibles</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <!-- Buscador con estilo moderno -->
                <div class="mb-3">
                    <div class="input-group shadow-sm">
                        <input type="text" id="searchBienes" class="form-control border-primary"
                            placeholder="Buscar por N.º Inventario o Nombre...">
                    </div>
                </div>

                <!-- Tabla con estilos mejorados -->
                <div class="table-responsive">
                    <table id="tablaBienes" class="table table-striped table-hover table-bordered align-middle">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th>N° INVENTARIO</th>
                                <th>NOMBRE</th>
                                <th>OBSERVACIONES</th>
                                <th>FECHA DE ADQUISICIÓN DEL BIEN</th>
                                <th>IMAGEN</th>
                                <th>
                                    <div class="select-all-container">
                                        SELECCIONAR <br>
                                        <input type="checkbox" id="selectAll">
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="bienesResultados">
                            <!-- Se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>

                <!-- Paginación personalizada -->
                <nav>
                    <ul class="pagination justify-content-center mt-3" id="paginacionBienes">
                        <!-- Se generará dinámicamente -->
                    </ul>
                </nav>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick="guardarSeleccion()">
                    <i class="bi bi-check-circle"></i> Guardar selección
                </button>
                <button class="btn btn-danger" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
