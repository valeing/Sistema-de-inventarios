@extends('layouts.app')


@section('content')
    <div class="container">
        <h2>Detalles de las Notificaciones</h2>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route("$prefix.dashboard") }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route("$prefix.notifications.index") }}">Lista de notificaciones</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalles de la notificación</li>
            </ol>
        </nav>

        <p><strong>Correo Electrónico:</strong> {{ $notification->email }}</p>

        <div class="accordion" id="notificationsAccordion">
            @foreach ($relatedNotifications as $index => $relatedNotification)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $index }}">
                            Notificación ID: {{ $relatedNotification->id }}
                        </button>
                    </h2>
                    <div id="collapse{{ $index }}"
                        class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                        aria-labelledby="heading{{ $index }}" data-bs-parent="#notificationsAccordion">
                        <div class="accordion-body">
                            <p><strong>ID:</strong> {{ $relatedNotification->id }}</p>
                            <p><strong>Nombre Completo:</strong> {{ $relatedNotification->nombre_completo }}</p>

                            <!-- Contraseña con botón de mostrar -->
                            <p><strong>Contraseña:</strong>
                                <span id="password-{{ $index }}" class="me-2"
                                    data-password="{{ Crypt::decrypt($relatedNotification->password) }}">********</span>
                                <button class="btn btn-link p-0 toggle-password" type="button"
                                    data-index="{{ $index }}">
                                    <i class="fas fa-eye-slash" id="icon-{{ $index }}"></i>
                                </button>
                            </p>

                            <p><strong>Fecha de Solicitud:</strong> {{ $relatedNotification->requested_at }}</p>
                            <p><strong>Estado:</strong> {{ $relatedNotification->seen ? 'Visto' : 'No visto' }}</p>

                            <!-- Botón para marcar como visto -->
                            @if (!$relatedNotification->seen)
                                <form action="{{ route("$prefix.notifications.markAsSeen", $relatedNotification->id) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">Marcar como visto</button>
                                </form>
                            @endif

                            <!-- Botón para eliminar -->
                            <form action="{{ route("$prefix.notifications.destroy", ['id' => $relatedNotification->id]) }}"
                                method="POST" style="display:inline;"
                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta notificación? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <a href="{{ route("$prefix.notifications.index") }}" class="btn btn-primary mt-3">Volver</a>
    </div>

    <!-- Script para mostrar/ocultar contraseña -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleButtons = document.querySelectorAll(".toggle-password");

            toggleButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const index = this.getAttribute("data-index");
                    const passwordSpan = document.getElementById(`password-${index}`);
                    const icon = this.querySelector("i");
                    const rawPassword = passwordSpan.getAttribute("data-password");

                    if (passwordSpan.textContent === "********") {
                        // Mostrar contraseña
                        passwordSpan.textContent = rawPassword;
                        icon.classList.remove("fa-eye-slash");
                        icon.classList.add("fa-eye");
                    } else {
                        // Ocultar contraseña
                        passwordSpan.textContent = "********";
                        icon.classList.remove("fa-eye");
                        icon.classList.add("fa-eye-slash");
                    }
                });
            });
        });
    </script>
@endsection
