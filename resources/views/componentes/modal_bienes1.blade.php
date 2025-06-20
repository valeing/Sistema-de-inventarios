<!-- Modal -->
<div id="modalBienes" class="modal fade" tabindex="-1" aria-labelledby="modalBienesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-box-seam"></i> Bienes disponibles</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <!-- Buscador -->
                <div class="mb-3">
                    <div class="input-group shadow-sm">
                        <input type="text" id="searchBienes" class="form-control border-primary"
                            placeholder="Buscar por N.º Inventario o Nombre...">
                    </div>
                </div>

                <!-- Tabla -->
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
                                    <div class="select-all-container">SELECCIONAR</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="bienesResultados">
                            <!-- Se carga con DataTables -->
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <nav>
                    <ul class="pagination justify-content-center mt-3" id="paginacionBienes"></ul>
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

<!-- Estilos -->
<style>
    .table thead {
        background-color: #0d6efd !important;
        color: white !important;
        text-align: center;
    }

    .table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    .table th,
    .table td {
        text-align: center;
        vertical-align: middle;
        padding: 6px 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 14px;
    }

    /* Asignar ancho aproximado por columna */
    #tablaBienes th:nth-child(1) {
        width: 18%;
    }

    #tablaBienes th:nth-child(2) {
        width: 18%;
    }

    #tablaBienes th:nth-child(3) {
        width: 18%;
    }

    #tablaBienes th:nth-child(4) {
        width: 15%;
    }

    #tablaBienes th:nth-child(5) {
        width: 15%;
    }

    #tablaBienes th:nth-child(6) {
        width: 16%;
    }

    /* Alternancia y hover */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(13, 110, 253, 0.05);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.1);
    }

    /* Imagen */
    #tablaBienes img {
        width: 45px;
        height: auto;
        border-radius: 4px;
        border: 1px solid #ccc;
        object-fit: cover;
    }

    .seleccionar-bien {
        font-size: 0.8rem;
        padding: 4px 8px;
    }

    .select-all-container {
        display: flex;
        align-items: center;
        justify-content: center;
    }

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

    .modal-body {
        overflow-x: auto;
    }
</style>
