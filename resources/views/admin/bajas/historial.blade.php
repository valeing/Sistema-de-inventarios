@extends('layouts.app')


@section('title', 'Historial de bajas')

@section('content')
    <div class="container mt-5">
        <h2>Historial de bajas</h2>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Historial de bajas</li>
            </ol>
        </nav>

        <!-- Botón para agregar nueva dirección -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route($prefix . '.bajas.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Registrar nueva baja
            </a>
        </div>

        <!-- Buscador y selector de fecha -->
        <div class="card p-3 mb-3">
            <form id="filtro-bajas" class="position-relative" method="GET" action="{{ route($prefix . '.bajas.index') }}">
                <div class="input-group">
                    <!-- Filtro de fecha -->

                    <select class="form-select" id="fecha_baja" name="fecha_baja" style="max-width: 200px;">
                        <option value="" {{ request('fecha_baja') == '' ? 'selected' : '' }}>Todas las fechas
                        </option>
                        <option value="hoy" {{ request('fecha_baja') == 'hoy' ? 'selected' : '' }}>Hoy</option>
                        <option value="semana" {{ request('fecha_baja') == 'semana' ? 'selected' : '' }}>Últimos 7 días
                        </option>
                        <option value="mes" {{ request('fecha_baja') == 'mes' ? 'selected' : '' }}>Últimos 30 días
                        </option>
                        <option value="personalizada" {{ request('fecha_baja') == 'personalizada' ? 'selected' : '' }}>
                            Fecha personalizada
                        </option>
                    </select>

                    <!-- Input de fecha personalizada (inicialmente oculto) -->
                    <input type="date" id="fecha_personalizada" name="fecha_personalizada"
                        class="form-control ms-2 d-none" value="{{ request('fecha_personalizada') }}">


                    <!-- Campo de búsqueda -->
                    <input type="text" class="form-control" id="buscar" name="buscar"
                        placeholder="Buscar por N.º de inventario o Nombre del bien" value="{{ request('buscar') }}">

                    <!-- Botón de búsqueda -->
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>

                <!-- Contenedor de sugerencias -->
                <div id="resultados-busqueda" class="list-group position-absolute w-100"
                    style="z-index: 1000; display: none;"></div>
            </form>
        </div>


        <div class="card p-4">
            <h4 class="mb-4">Lista de de bajas</h4>
            <!-- Tabla de bajas -->
            <div id="tabla-bajas">
                @if ($bajas->isEmpty())
                    <div class="alert alert-info text-center">
                        No se encontraron registros de bajas en la fecha
                        seleccionada.
                    </div>
                @else
                    @include('admin.bajas.partials.bajas-table')

                    <!-- Paginación con Bootstrap 5 -->
                    <div class="d-flex justify-content-center my-3">
                        {{ $bajas->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif
            </div>


        </div>


    </div>




    <!-- CSS para la lista de búsqueda con scroll -->
    <style>
        #resultados-busqueda {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        #resultados-busqueda a {
            padding: 10px;
            display: block;
            color: #333;
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #resultados-busqueda a:hover {
            background-color: #f1f1f1;
        }
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const selectFecha = document.getElementById("fecha_baja");
            const inputFechaPersonalizada = document.getElementById("fecha_personalizada");
            const formFiltro = document.getElementById("filtro-bajas");

            // ✅ Mostrar el input de fecha personalizada cuando se elige esa opción
            selectFecha.addEventListener("change", function() {
                if (this.value === "personalizada") {
                    inputFechaPersonalizada.classList.remove("d-none");
                    inputFechaPersonalizada.focus();
                } else {
                    inputFechaPersonalizada.classList.add("d-none");
                    inputFechaPersonalizada.value = "";
                }
            });

            // ✅ Si la fecha personalizada ya está seleccionada en la URL, mostrar el input
            if (selectFecha.value === "personalizada") {
                inputFechaPersonalizada.classList.remove("d-none");
            }

            // ✅ Actualizar automáticamente el formulario al seleccionar una fecha personalizada
            inputFechaPersonalizada.addEventListener("change", function() {
                formFiltro.submit();
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputBuscar = document.getElementById("buscar");
            const selectFecha = document.getElementById("fecha_baja");
            const resultadosBusqueda = document.getElementById("resultados-busqueda");
            const botonBuscar = document.querySelector("button[type='submit']");
            let todasLasBajas = [];

            // ✅ Mantener la selección en el filtro de fechas
            selectFecha.value = new URLSearchParams(window.location.search).get("fecha_baja") || "";

            // 🔍 Búsqueda en tiempo real sin necesidad de presionar enter
            inputBuscar.addEventListener("keyup", function() {
                let texto = inputBuscar.value.toLowerCase().trim();
                if (texto.length < 1) {
                    resultadosBusqueda.style.display = "none";
                    return;
                }

                let resultados = todasLasBajas.filter(baja =>
                    baja.numero_inventario.includes(texto) || baja.nombre.toLowerCase().includes(texto)
                );

                let listaResultados = resultados.slice(0, 6).map(baja =>
                    `<a href="${window.location.origin}/{{ $prefix }}/bajas/${baja.id}/ver" class="list-group-item list-group-item-action">
                    <strong>${baja.numero_inventario}</strong> - ${baja.nombre} (${baja.fecha_baja})
                </a>`
                ).join("");

                resultadosBusqueda.innerHTML = listaResultados ||
                    '<div class="list-group-item">No se encontraron resultados</div>';
                resultadosBusqueda.style.display = "block";
            });

            // 🖱️ Ocultar lista de búsqueda cuando se haga clic fuera
            document.addEventListener("click", function(event) {
                if (!event.target.closest("#buscar, #resultados-busqueda")) {
                    resultadosBusqueda.style.display = "none";
                }
            });

            // ✅ Cargar todas las bajas al inicio (para búsqueda en vivo)
            fetch("{{ route($prefix . '.bajas.all') }}")
                .then(response => response.json())
                .then(data => {
                    todasLasBajas = data;
                })
                .catch(error => console.error("Error al cargar bajas:", error));

            // 🛑 Evitar que la página se actualice automáticamente al cambiar la fecha
            selectFecha.addEventListener("change", function() {
                // No recarga la página hasta que se haga clic en el botón de búsqueda
            });

            // ✅ Al hacer clic en el botón de búsqueda, se actualiza la página con los filtros aplicados
            botonBuscar.addEventListener("click", function(event) {
                event.preventDefault(); // 🔹 Evita la recarga automática
                let search = inputBuscar.value.trim();
                let fecha_baja = selectFecha.value;
                let url = new URL(window.location.href);

                // Mantener el filtro activo en la URL
                url.searchParams.delete("buscar");
                url.searchParams.delete("fecha_baja");

                if (search) {
                    url.searchParams.set("buscar", search);
                }

                if (fecha_baja !== "") {
                    url.searchParams.set("fecha_baja", fecha_baja);
                }

                window.location.href = url.toString(); // 🔹 Redirige con los filtros aplicados
            });

            // 📌 Mensajes de alerta con SweetAlert2
            let successMessage = @json(session('success'));
            let errorMessages = @json($errors->all());

            if (successMessage) {
                Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: successMessage,
                    confirmButtonColor: "#28a745",
                });
            }

            if (errorMessages.length > 0) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    html: errorMessages.join("<br>"),
                    confirmButtonColor: "#d33",
                });
            }
        });
    </script>


@endsection
