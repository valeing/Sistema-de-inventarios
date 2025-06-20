<table class="table table-bordered text-center">
    <thead class="table-primary">
        <tr>
            <th>N° de Inventario</th>
            <th>Nombre</th>
            <th>Observaciones</th>
            <th>Fecha de adquisición</th>
            <th>Imagen</th>
            <th>Seleccionar</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bienes_disponibles as $bien)
            <tr>
                <td>{{ $bien->numero_inventario }}</td>
                <td>{{ $bien->nombre }}</td>
                <td>{{ $bien->observaciones }}</td>
                <td>{{ \Carbon\Carbon::parse($bien->fecha_adquisicion)->format('d/m/Y') }}</td>
                <td>
                    @if ($bien->imagen)
                        <img src="data:{{ $bien->mime_type }};base64,{{ $bien->imagen }}" alt="Imagen del bien"
                            width="50">
                    @else
                        <img src="https://via.placeholder.com/50" alt="Imagen no disponible">
                    @endif
                </td>
                <td>
                    <!-- ✅ SE AGREGARON LOS ATRIBUTOS data-nombre Y data-inventario -->
                    <input type="checkbox" class="bien-checkbox" value="{{ $bien->id_bien }}"
                        data-nombre="{{ $bien->nombre }}" data-inventario="{{ $bien->numero_inventario }}">
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">No hay bienes disponibles</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Paginación personalizada -->
<div class="d-flex justify-content-center my-3">
    @if ($bienes_disponibles->hasPages())
        <ul class="pagination">
            {{-- Botón para la página anterior --}}
            @if ($bienes_disponibles->onFirstPage())
                <li class="page-item disabled"><span class="page-link">Anterior</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $bienes_disponibles->previousPageUrl() }}"
                        rel="prev"
                        onclick="event.preventDefault(); buscarBienes('{{ $bienes_disponibles->previousPageUrl() }}');">Anterior</a>
                </li>
            @endif

            {{-- Enlaces de paginación personalizados --}}
            @foreach ($bienes_disponibles->getUrlRange(max(1, $bienes_disponibles->currentPage() - 1), min($bienes_disponibles->lastPage(), $bienes_disponibles->currentPage() + 1)) as $page => $url)
                @if ($page == $bienes_disponibles->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $url }}"
                            onclick="event.preventDefault(); buscarBienes('{{ $url }}');">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Botón para la página siguiente --}}
            @if ($bienes_disponibles->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $bienes_disponibles->nextPageUrl() }}"
                        rel="next"
                        onclick="event.preventDefault(); buscarBienes('{{ $bienes_disponibles->nextPageUrl() }}');">Siguiente</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">Siguiente</span></li>
            @endif
        </ul>
    @endif
</div>
