@extends('layouts.app')


@section('title', 'Ver bienes')

@section('content')
    <div class="container mt-4">
        <h2>Bienes</h2>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bienes</li>
            </ol>
        </nav>
        <!-- Botón para agregar nueva dirección -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route("$prefix.bienes.create") }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Registrar nuevo bien
            </a>
        </div>

        <div class="card p-3 mb-3">
            <form class="position-relative" method="GET" action="{{ route("$prefix.bienes.index") }}">
                <div class="input-group">
                    <!-- Selector de Categoría -->
                    <select class="form-select" id="categoria" name="categoria" style="max-width: 200px;">
                        <option value="">Todas las categorías</option>
                        <option value="Equipo tecnológico"
                            {{ request('categoria') == 'Equipo tecnológico' ? 'selected' : '' }}>
                            Equipo tecnológico
                        </option>
                        <option value="Mobiliario" {{ request('categoria') == 'Mobiliario' ? 'selected' : '' }}>
                            Mobiliario
                        </option>
                    </select>

                    <!-- Campo de búsqueda -->
                    <input type="text" class="form-control" id="buscarBien" name="search"
                        placeholder="Buscar por N° de Inventario, N° de Serie, Nombre o Resguardante"
                        value="{{ request('search') }}">

                    <!-- Botón de búsqueda -->
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>

                <!-- Contenedor de sugerencias -->
                <div id="bienResultados" class="list-group mt-1 position-absolute d-none"></div>
            </form>
        </div>






        <div class="card p-4">
            <h4 class="mb-4">Lista de bienes Registrados</h4>

            @if ($bienes->isEmpty())
                <div class="alert alert-info text-center">
                    No hay bienes registrados.
                </div>
            @else
                <!-- Tabla de bienes -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th class="text-uppercase">
                                    <div style="display: flex; flex-direction: column; align-items: center;">
                                        <span>Todos</span>
                                        <input type="checkbox" id="selectAll">
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
                                        <input type="checkbox" name="bienes[]" value="{{ $bien->id_bien }}"
                                            class="bien-checkbox">
                                    </td>
                                    <td>{{ $bien->numero_inventario }}</td>
                                    <td>{{ $bien->nombre }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($bien->observaciones, 15, '...') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bien->fecha_adquisicion)->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        @if ($bien->imagen)
                                            <img src="data:{{ $bien->mime_type }};base64,{{ $bien->imagen }}"
                                                alt="Imagen del bien" width="50">
                                        @else
                                            <img src="https://via.placeholder.com/50" alt="Imagen no disponible">
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-3">
                                            <a href="{{ route("$prefix.bienes.show", $bien->id_bien) }}" class="text-info"
                                                title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route("$prefix.bienes.edit", $bien->id_bien) }}"
                                                class="text-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn p-0 text-danger" title="Eliminar"
                                                onclick="confirmDelete('{{ $bien->estado }}', '{{ $bien->asignacion ? true : false }}', '{{ $bien->id_bien }}', '{{ route("$prefix.bienes.destroy", $bien->id_bien) }}')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Formulario para descargar etiquetas -->
                <form id="descargarEtiquetasPDF" action="{{ route("$prefix.bienes.descargar_etiquetas") }}" method="POST">
                    @csrf
                    <input type="hidden" id="selectedBienes" name="bienes">
                    <div class="table-responsive">
                        <div class="d-flex justify-content-center gap-3 mt-3">
                            <a href="{{ route("$prefix.bienes.exportar.todos") }}" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Exportar todos los bienes
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-qrcode"></i> Descargar etiquetas PDF
                            </button>
                        </div>
                    </div>
                </form>


                <!-- Paginación Bootstrap -->
                @if ($bienes->hasPages())
                    <nav class="d-flex justify-content-center mt-3">
                        {{ $bienes->links('pagination::bootstrap-5') }}
                    </nav>
                @endif



            @endif
        </div>

        <!-- Sección de Importar Excel -->
        <div class="card p-3 mt-4 mb-5">
            <h5 class="mb-3">Importar bienes desde Excel</h5>
            <form action="{{ route("$prefix.bienes.importar.bienes") }}" method="POST" enctype="multipart/form-data"
                class="d-flex align-items-center">
                @csrf
                <input type="file" name="archivo" accept=".xlsx, .csv" class="form-control me-2" required
                    style="height: 38px;">
                <button type="submit" class="btn btn-warning d-flex align-items-center px-3" style="height: 38px;">
                    <i class="fas fa-upload me-1"></i> Importar
                </button>
            </form>
        </div>
    </div>


    <style>
        /* Contenedor de sugerencias ajustado */
        #bienResultados {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            width: 100%;
            /* Se ajusta completamente al input */
            position: absolute;
            top: 100%;
            left: 0;
        }

        /* Elemento individual de cada sugerencia */
        .bien-item {
            padding: 12px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s ease-in-out;
        }

        .bien-item:hover {
            background-color: #f8f9fa;
        }

        /* Mejorar el diseño del texto dentro de cada sugerencia */
        .bien-item strong {
            font-size: 14px;
            color: #333;
        }

        .bien-item small {
            font-size: 12px;
            color: #666;
        }
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buscarBienInput = document.getElementById('buscarBien');
            const categoriaSelect = document.getElementById('categoria');
            const bienResultados = document.getElementById('bienResultados');

            // Obtener el prefijo del rol dinámicamente desde el atributo data
            const prefix = document.body.getAttribute('data-prefix') || 'admin';

            function actualizarSugerencias() {
                const query = buscarBienInput.value.trim().toLowerCase();
                const categoriaSeleccionada = categoriaSelect.value;

                if (query.length < 2) {
                    bienResultados.classList.add('d-none');
                    return;
                }

                fetch(`/${prefix}/bienes?search=${query}&categoria=${categoriaSeleccionada}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        bienResultados.innerHTML = '';
                        if (data.length > 0) {
                            bienResultados.classList.remove('d-none');
                            data.forEach(bien => {
                                const item = document.createElement('button');
                                item.type = 'button';
                                item.classList.add('list-group-item', 'list-group-item-action',
                                    'bien-item');
                                item.innerHTML = `
                                    <strong>${bien.nombre}</strong>
                                    <small>N° Inventario: ${bien.numero_inventario} | N° Serie: ${bien.numero_serie} | Costo: ${bien.costo}</small>
                                    <small>Resguardante: ${bien.resguardante}</small>
                                `;
                                item.addEventListener('click', () => window.location.href =
                                    `/${prefix}/bienes/${bien.id}/ver`);
                                bienResultados.appendChild(item);
                            });
                        } else {
                            bienResultados.classList.add('d-none');
                        }
                    })
                    .catch(error => console.error('Error en la búsqueda:', error));
            }

            buscarBienInput.addEventListener('input', actualizarSugerencias);
            categoriaSelect.addEventListener('change', actualizarSugerencias);
        });
    </script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let successMessage = @json(session('success'));
            let errorMessage = @json(session('error'));

            if (successMessage) {
                Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: successMessage,
                    confirmButtonColor: "#28a745",
                });
            }

            if (errorMessage) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: errorMessage,
                    confirmButtonColor: "#d33",
                });
            }
        });

        function confirmDelete(estado, asignado, id_bien, url) {
            if (estado === 'activo') {
                Swal.fire('No permitido', 'El bien está activo y no se puede eliminar.', 'warning');
                return;
            }
            if (asignado === 'true') {
                Swal.fire('No permitido', 'El bien está asignado a un resguardante y no se puede eliminar.', 'warning');
                return;
            }

            Swal.fire({
                title: `¿Eliminar bien #${id_bien}?`,
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }


        document.addEventListener("DOMContentLoaded", function() {
            const selectAllCheckbox = document.getElementById("selectAll");
            const checkboxes = document.querySelectorAll(".bien-checkbox");
            const form = document.getElementById("descargarEtiquetasPDF");
            const selectedBienesInput = document.getElementById("selectedBienes");

            // Función para seleccionar/deseleccionar todos los checkboxes
            selectAllCheckbox.addEventListener("change", function() {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = this.checked;
                });
            });

            form.addEventListener("submit", function(event) {
                event.preventDefault(); // Prevenir envío por defecto

                const selectedBienes = [];
                checkboxes.forEach((checkbox) => {
                    if (checkbox.checked) {
                        selectedBienes.push(checkbox.value);
                    }
                });

                if (selectedBienes.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Atención',
                        text: 'Selecciona al menos un bien para descargar su etiqueta.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                selectedBienesInput.value = JSON.stringify(selectedBienes); // Enviar como JSON
                form.submit(); // Enviar el formulario después de actualizar los valores
            });

        });
    </script>

@endsection
