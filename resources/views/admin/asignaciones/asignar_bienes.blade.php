@extends('layouts.app')
@section('title', 'Asignar bienes')

@section('content')
    <style>
        /* Estilos para el contenedor de bienes seleccionados con scroll */
        .bienes-seleccionados {
            max-height: 250px;
            /* Límite de altura */
            overflow-y: auto;
            /* Habilita el scroll */
            border: 1px solid #ddd;
            padding: 10px;
            background: #fff;
            border-radius: 5px;
        }

        .bienes-lista {
            display: flex;
            flex-direction: column;
        }

        .bien {
            padding: 8px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-remove {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
    <div class="container mt-5">
        <h2>Asignar bienes</h2>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.asignaciones.index") }}">Asignaciones</a></li>
                <li class="breadcrumb-item active" aria-current="page">Asignar bienes</li>
            </ol>
        </nav>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Formulario para asignar bienes -->
        <form action="{{ route("$prefix.asignaciones.store") }}" method="POST" id="formAsignarBienes"
            onsubmit="return validarFormulario()">
            @csrf

            <!-- Selección de Bienes -->
            <div class="card p-4">
                <h5>Bienes seleccionados</h5>

                <!-- Contenedor de bienes seleccionados con scroll -->
                <div id="bienesSeleccionados" class="bienes-seleccionados mb-3">
                    <div class="bienes-lista" id="bienesLista">
                        <p id="mensajeSeleccion" class="text-muted">No hay bienes seleccionados.</p>
                    </div>
                </div>

                <!-- Botón para abrir el modal -->
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalBienes">
                    Seleccionar bienes
                </button>

                <div id="errorBienes" class="text-danger mt-2"></div>
            </div>



            <!-- Selección de Resguardante -->
            <div class="card p-4 mt-4">
                <h5>Resguardante</h5>
                <div class="mb-3">
                    <label for="buscarResguardante" class="form-label">Buscar resguardante por nombre o número de
                        empleado</label>
                    <input type="text" class="form-control" id="buscarResguardante" placeholder="Escribe para buscar...">
                    <div id="resguardanteResultados" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;">
                    </div>
                    <input type="hidden" name="id_resguardante" id="id_resguardante" required>
                    <div id="mensajeResguardante" class="text-danger mt-1"></div>
                </div>

                <div class="mb-3">
                    <label for="fechaAsignacion" class="form-label">Fecha de asignación</label>
                    <input type="date" class="form-control" id="fechaAsignacion" name="fecha_asignacion"
                        value="{{ old('fecha_asignacion') }}" required>
                </div>

                <div class="d-flex justify-content-center">
                    <button class="btn btn-success mx-2" type="submit">Asignar bienes</button>
                    <button class="btn btn-danger mx-2" type="button" onclick="window.history.back()">Atrás</button>
                </div>
            </div>
        </form>
    </div>

    @include('componentes.modal_bienes')

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let bienesSeleccionados = {};
            const prefix = document.body.getAttribute('data-prefix') || 'admin';

            let table = $('#tablaBienes').DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                searching: false,
                info: false,
                lengthChange: false,
                ordering: false,
                pageLength: 10,
                autoWidth: false,
                scrollX: false,
                language: {
                    paginate: {
                        previous: "«",
                        next: "»"
                    },
                    zeroRecords: "No se encontraron bienes.",
                    loadingRecords: "Cargando bienes...",
                    processing: "Procesando...",
                },
                ajax: {
                    url: `/${prefix}/asignaciones/get-bienes`,
                    type: 'GET',
                    data: function(d) {
                        d.search = $('#searchBienes').val();
                    },
                    error: function(xhr) {
                        console.error("Error cargando bienes:", xhr.responseText);
                    }
                },
                columns: [{
                        data: 'numero_inventario',
                        className: "text-center",
                        width: "18%"
                    },
                    {
                        data: 'nombre',
                        className: "text-center",
                        width: "20%"
                    },
                    {
                        data: 'observaciones',
                        className: "text-center",
                        width: "18%"
                    },
                    {
                        data: 'fecha_adquisicion',
                        className: "text-center",
                        width: "15%"
                    },
                    {
                        data: 'imagen',
                        className: "text-center",
                        width: "12%",
                        render: function(data, type, row) {
                            return data ?
                                `<img src="data:${row.mime_type};base64,${data}" style="width: 45px; height: auto; border-radius: 3px; border: 1px solid #ccc;" alt="Imagen del bien">` :
                                `<img src="https://via.placeholder.com/45" style="width: 45px; height: auto; border-radius: 3px; border: 1px solid #ccc;" alt="Imagen no disponible">`;
                        }
                    },
                    {
                        data: null,
                        className: "text-center",
                        width: "10%",
                        render: function(data, type, row) {
                            let checked = bienesSeleccionados[row.id_bien] ? 'checked' : '';
                            return `
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" class="bien-checkbox form-check-input"
                                        value="${row.id_bien}" data-nombre="${row.nombre}"
                                        data-inventario="${row.numero_inventario}" ${checked}>
                                </div>`;
                        }
                    }
                ],
                drawCallback: function() {
                    ajustarEstilosCompactos();
                    actualizarSeleccionadosEnTabla();
                    verificarSeleccionGeneral();
                    estilizarPaginacion();
                }
            });

            $('#searchBienes').on('input', function() {
                table.ajax.reload();
            });

            $('#selectAll').on('change', function() {
                let isChecked = this.checked;
                $('.bien-checkbox').each(function() {
                    let id = $(this).val();
                    let nombre = $(this).data('nombre');
                    let inventario = $(this).data('inventario');

                    if (isChecked) {
                        $(this).prop('checked', true);
                        bienesSeleccionados[id] = {
                            nombre,
                            inventario
                        };
                    } else {
                        $(this).prop('checked', false);
                        delete bienesSeleccionados[id];
                    }
                });

                actualizarListaSeleccionados();
            });

            $(document).on('change', '.bien-checkbox', function() {
                let id = $(this).val();
                let nombre = $(this).data('nombre');
                let inventario = $(this).data('inventario');

                if ($(this).prop('checked')) {
                    bienesSeleccionados[id] = {
                        nombre,
                        inventario
                    };
                } else {
                    delete bienesSeleccionados[id];
                }

                actualizarListaSeleccionados();
                verificarSeleccionGeneral();
            });

            $(document).on('click', '.quitar-bien', function() {
                let id = $(this).data('id');
                delete bienesSeleccionados[id];
                actualizarListaSeleccionados();
                table.$(`.bien-checkbox[value='${id}']`).prop('checked', false);
                verificarSeleccionGeneral();
            });

            function actualizarListaSeleccionados() {
                let lista = document.getElementById("bienesSeleccionados");
                lista.innerHTML = "";

                if (Object.keys(bienesSeleccionados).length === 0) {
                    lista.innerHTML = '<p class="text-muted">No hay bienes seleccionados.</p>';
                } else {
                    Object.keys(bienesSeleccionados).forEach(id => {
                        let bien = bienesSeleccionados[id];
                        lista.innerHTML += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${bien.nombre} (N° Inventario: ${bien.inventario})
                                <button class="btn btn-sm btn-danger quitar-bien" data-id="${id}">X</button>
                                <input type="hidden" name="id_bien[]" value="${id}">
                            </li>`;
                    });
                }
            }

            function verificarSeleccionGeneral() {
                let total = $('.bien-checkbox').length;
                let marcados = $('.bien-checkbox:checked').length;
                $('#selectAll').prop('checked', total > 0 && total === marcados);
            }

            function actualizarSeleccionadosEnTabla() {
                $('.bien-checkbox').each(function() {
                    let id = $(this).val();
                    $(this).prop('checked', !!bienesSeleccionados[id]);
                });

                verificarSeleccionGeneral();
            }

            function estilizarPaginacion() {
                let pagination = $('.dataTables_paginate');
                pagination.addClass('pagination justify-content-center mt-3');
                pagination.find('a').addClass('page-link');
                pagination.find('li').addClass('page-item');
                pagination.find('.current').parent().addClass('active');
            }

            function ajustarEstilosCompactos() {
                $('#tablaBienes').css('table-layout', 'fixed');
                $('#tablaBienes th, #tablaBienes td').css({
                    'white-space': 'nowrap',
                    'overflow': 'hidden',
                    'text-overflow': 'ellipsis',
                    'padding': '6px'
                });
            }

            window.guardarSeleccion = function() {
                actualizarListaSeleccionados();
                $('#modalBienes').modal('hide');
            };

            $('#modalBienes').on('shown.bs.modal', function() {
                table.ajax.reload();
            });

            $('#asignarBienesForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serializeArray();

                if (Object.keys(bienesSeleccionados).length === 0) {
                    alert("Debe seleccionar al menos un bien.");
                    return;
                }

                Object.keys(bienesSeleccionados).forEach(id => {
                    formData.push({
                        name: "id_bien[]",
                        value: id
                    });
                });

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            alert("Bienes asignados correctamente.");
                            bienesSeleccionados = {};
                            actualizarListaSeleccionados();
                            table.ajax.reload();
                        } else {
                            alert("Error al asignar bienes: " + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert("Hubo un error en la asignación.");
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <script src="{{ asset('js/buscar-resguardante.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const bienesLista = document.getElementById("bienesLista");
            const mensajeSeleccion = document.getElementById("mensajeSeleccion");

            function agregarBien(nombre, inventario) {
                // Si es el primer bien, eliminamos el mensaje
                if (bienesLista.children.length === 1 && mensajeSeleccion) {
                    mensajeSeleccion.style.display = "none";
                }

                // Crear el nuevo bien
                const div = document.createElement("div");
                div.classList.add("bien");
                div.innerHTML = `
                    ${nombre} (N° Inventario: ${inventario})
                    <button class="btn-remove" onclick="eliminarBien(this)">X</button>
                `;
                bienesLista.appendChild(div);
                verificarScroll();
            }

            function eliminarBien(btn) {
                btn.parentElement.remove();
                verificarScroll();
            }

            function verificarScroll() {
                const bienes = bienesLista.children.length;
                if (bienes > 5) {
                    bienesLista.parentElement.style.maxHeight = "250px";
                    bienesLista.parentElement.style.overflowY = "auto";
                } else {
                    bienesLista.parentElement.style.maxHeight = "none";
                    bienesLista.parentElement.style.overflowY = "visible";
                }

                // Si ya no hay bienes, mostramos el mensaje de "No hay bienes seleccionados"
                if (bienes === 0 && mensajeSeleccion) {
                    mensajeSeleccion.style.display = "block";
                }
            }
        });
    </script>

@endsection
