@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Notificaciones</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lista de notificaciones</li>
            </ol>
        </nav>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($groupedNotifications->count() > 0)
            <div class="card p-4">
                <h4 class="mb-4">Tabla de notificaciones</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th class="text-uppercase">Nombre Completo</th>
                                <th class="text-uppercase">Email</th>
                                <th class="text-uppercase">Contraseña</th>
                                <th class="text-uppercase">Fecha de Solicitud</th>
                                <th class="text-uppercase">Visto</th>
                                <th class="text-uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedNotifications as $email => $notificationsGroup)
                                <tr class="table-light">
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;"
                                            title="{{ $notificationsGroup->last()->nombre_completo }}">
                                            {{ $notificationsGroup->last()->nombre_completo }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-truncate" style="max-width: 200px;"
                                                title="{{ $email }}">
                                                {{ $email }}
                                            </span>
                                            <span class="badge bg-info text-dark ms-2" title="Número de notificaciones">
                                                {{ $notificationsGroup->count() }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center text-muted">*****</td>
                                    <td class="text-center">
                                        {{ $notificationsGroup->last()->requested_at }}
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="{{ $notificationsGroup->where('seen', false)->count() > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                            {{ $notificationsGroup->where('seen', false)->count() > 0 ? 'No' : 'Sí' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-3">
                                            <a href="{{ route("$prefix.notifications.show", ['id' => $notificationsGroup->last()->id]) }}"
                                                class="text-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form
                                                action="{{ route("$prefix.notifications.markAllAsSeen", ['email' => $email]) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn p-0 text-success"
                                                    title="Marcar como visto">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                            <button class="btn p-0 text-danger" title="Eliminar todas"
                                                onclick="confirmDelete('{{ route("$prefix.notifications.destroyAll", ['email' => $email]) }}', '{{ $email }}')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>



            <!-- Paginación -->
            @if ($notifications->hasPages())
                <div class="d-flex justify-content-center my-3">
                    {{ $notifications->links('vendor.pagination.bootstrap-5') }}
                </div>
            @endif
        @else
            <p class="text-center text-muted">No hay notificaciones disponibles.</p>
        @endif
    </div>

    <!-- SweetAlert2 para confirmar eliminación -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(url, email) {
            if (!url || url.includes("undefined")) {
                Swal.fire('Error', 'No se puede eliminar. URL inválida.', 'error');
                return;
            }

            Swal.fire({
                title: `¿Estás seguro de eliminar todas las notificaciones de ${email}?`,
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

            return false;
        }
    </script>
@endsection
