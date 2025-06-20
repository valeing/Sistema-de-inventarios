@extends('layouts.app')


@section('content')
    <div class="container mt-5">
        <h2>Inventario Físico</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Inventario físico</li>
            </ol>
        </nav>
        <!-- Botón para agregar nueva dirección -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route($prefix . '.inventario_fisico.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Programar Inventario
            </a>
        </div>

        <div class="card p-4">
            <h4 class="mb-4">Lista del Inventario Físico</h4>

            @if ($inventarios->isEmpty())
                <div class="alert alert-info text-center">
                    No hay inventarios físicos programados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle text-center">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Inicio</th>
                                <th>Finalización</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inventarios as $inventario)
                                <tr>
                                    <td>{{ $inventario->nombre_inventario }}</td>
                                    <td>{{ $inventario->descripcion }}</td>
                                    <td>{{ $inventario->fecha_inicio }}</td>
                                    <td>{{ $inventario->fecha_finalizacion }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $inventario->estado == 'Completado' ? 'success' : 'warning' }}">
                                            {{ $inventario->estado }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex justify-content-center gap-3">
                                            <a href="{{ route($prefix . '.inventario_fisico.show', $inventario->id) }}"
                                                class="text-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route($prefix . '.inventario_fisico.edit', $inventario->id) }}"
                                                class="text-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn p-0 text-danger delete-btn"
                                                data-url="{{ route($prefix . '.inventario_fisico.destroy', $inventario->id) }}"
                                                title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($inventarios->hasPages())
            <div class="d-flex justify-content-center my-3">
                {{ $inventarios->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif

    </div>

    <!-- SweetAlert2 para mensajes de éxito y error -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let successMessage = @json(session('success'));
            let errorMessage = @json(session('error'));

            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: successMessage,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'OK'
                });
            }

            if (errorMessage) {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: errorMessage,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        });

        // Confirmación de eliminación con SweetAlert2
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                let deleteUrl = this.dataset.url;
                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "Esta acción no se puede deshacer.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;
                        form.innerHTML = `@csrf @method('DELETE')`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
