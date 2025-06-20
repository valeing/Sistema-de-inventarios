<!-- Modal Comentario -->
<div class="modal fade" id="comentarioModal{{ $reporte->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Comentario del resguardante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ $reporte->comentario }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rechazo -->
<div class="modal fade" id="rechazoModal{{ $reporte->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Motivo de rechazo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ $reporte->comentario_rechazo }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalles -->
<div class="modal fade" id="modalDetalles{{ $reporte->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Reporte y del Bien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex gap-4">
                <div class="border rounded p-3 w-50">
                    <h6 class="text-primary">Detalles del Reporte</h6>
                    <p><strong>Comentario:</strong><br>{{ $reporte->comentario }}</p>
                    <p><strong>Motivo de rechazo:</strong><br>{{ $reporte->comentario_rechazo ?? '---' }}</p>
                    <p><strong>Fecha:</strong><br>{{ $reporte->fecha_reporte }}</p>
                    <p><strong>Estado:</strong><br>{{ ucfirst($reporte->estatus) }}</p>
                </div>
                <div class="border rounded p-3 w-50">
                    <h6 class="text-success">Detalles del Bien</h6>
                    <p><strong>Nombre:</strong><br>{{ $reporte->bien->nombre }}</p>
                    <p><strong>Estado:</strong><br>{{ ucfirst($reporte->bien->estado) }}</p>
                    <p><strong>Departamento:</strong><br>{{ $reporte->bien->departamento->nombre ?? '---' }}</p>
                    <p><strong>Categoría:</strong><br>{{ $reporte->bien->categoria }}</p>
                    <p><strong>Adquisición:</strong><br>{{ $reporte->bien->fecha_adquisicion }}</p>
                    <p><strong>N° Serie:</strong><br>{{ $reporte->bien->numero_serie }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
