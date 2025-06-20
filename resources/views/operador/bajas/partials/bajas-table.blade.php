<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle text-center">
        <thead class="bg-primary text-white text-center">
            <tr>
                <th>N.º de Inventario</th>
                <th>Nombre del bien</th>
                <th>Fecha de Baja</th>
                <th>Motivo de Baja</th>
                <th>Comentarios</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bajas as $baja)
                <tr class="table-light">
                    <td>{{ $baja->numero_inventario }}</td>
                    <td>{{ $baja->nombre }}</td>
                    <td>{{ $baja->fecha_baja }}</td>
                    <td>{{ $baja->motivo }}</td>
                    <td>
                        <span class="comentario-texto" title="{{ $baja->descripcion_problema }}">
                            {{ Str::limit($baja->descripcion_problema, 50, '...') }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route($prefix . '.bajas.show', $baja->id) }}" class="text-info" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
