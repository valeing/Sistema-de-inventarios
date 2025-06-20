<table class="table table-striped table-bordered align-middle">
    <thead class="bg-primary text-white text-center">
        <tr>
            <th class="text-uppercase">
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <span>Todos</span>
                    <input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes(this)" style="margin-top: 5px;">
                </div>
            </th>
            <th class="text-uppercase">N° de Inventario</th>
            <th class="text-uppercase">Nombre</th>
            <th class="text-uppercase">Observaciones</th>
            <th class="text-uppercase">Fecha de Adquisición del bien</th>
            <th class="text-uppercase">Imagen</th>
            <th class="text-uppercase">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($bienes as $bien)
            <tr class="fila-bien table-light" data-categoria="{{ $bien->categoria }}">
                <td class="text-center">
                    <input type="checkbox" name="bienes[]" value="{{ $bien->id_bien }}">
                </td>
                <td>{{ $bien->numero_inventario }}</td>
                <td>{{ $bien->nombre }}</td>
                <td>{{ \Illuminate\Support\Str::limit($bien->observaciones, 15, '...') }}</td>
                <td>{{ \Carbon\Carbon::parse($bien->fecha_adquisicion)->format('d/m/Y') }}</td>
                <td class="text-center">
                    @if ($bien->imagen)
                        <img src="data:{{ $bien->mime_type }};base64,{{ $bien->imagen }}" alt="Imagen del bien" width="50">
                    @else
                        <img src="https://via.placeholder.com/50" alt="Imagen no disponible">
                    @endif
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('bienes.show', $bien->id_bien) }}" class="text-info" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('bienes.edit', $bien->id_bien) }}" class="text-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn p-0 text-danger" title="Eliminar"
                            onclick="confirmDelete('{{ $bien->estado }}', '{{ $bien->asignacion ? true : false }}', '{{ $bien->id_bien }}', '{{ route('bienes.destroy', $bien->id_bien) }}')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
