@extends('layouts.app')


@section('title', 'Direcciones y Departamentos')

@section('content')
    <div class="container mt-5">
        <h2>Direcciones y Departamentos</h2>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Direcciones y Departamentos</li>
            </ol>
        </nav>

        <!-- Botón para agregar nueva dirección -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route("$prefix.direct.create") }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Registrar dirección y departamentos
            </a>
        </div>

        <div class="card p-4">
            <h4 class="mb-4">Listado de Direcciones y Departamentos</h4>

            @if ($direcciones->isEmpty())
                <div class="alert alert-info text-center">No hay direcciones registradas.</div>
            @else
                <!-- Tabla de direcciones y departamentos -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th class="text-uppercase">Dirección</th>
                                <th class="text-uppercase">Departamentos</th>
                                <th class="text-uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($direcciones as $direccion)
                                <tr class="table-light">
                                    <td>{{ $direccion->nombre }}</td>
                                    <td>{{ $direccion->departamentos->pluck('nombre')->implode(', ') ?: 'Sin departamentos' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-3">
                                            <a href="{{ route("$prefix.direct.edit", $direccion->id_direccion) }}"
                                                class="text-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $direccion->id_direccion }}"
                                                action="{{ route("$prefix.direct.destroy", $direccion->id_direccion) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn p-0 text-danger" title="Eliminar"
                                                    onclick="confirmDelete('{{ $direccion->nombre }}', 'delete-form-{{ $direccion->id_direccion }}')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3">
                    @if ($direcciones->hasPages())
                        <nav>
                            <ul class="pagination">
                                @if ($direcciones->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $direcciones->previousPageUrl() }}"
                                            rel="prev">&laquo;</a></li>
                                @endif

                                @foreach ($direcciones->getUrlRange(max(1, $direcciones->currentPage() - 1), min($direcciones->lastPage(), $direcciones->currentPage() + 1)) as $page => $url)
                                    @if ($page == $direcciones->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                @if ($direcciones->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $direcciones->nextPageUrl() }}"
                                            rel="next">&raquo;</a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        function confirmDelete(nombre, formId) {
            Swal.fire({
                title: `¿Eliminar la dirección "${nombre}"?`,
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>

@endsection
