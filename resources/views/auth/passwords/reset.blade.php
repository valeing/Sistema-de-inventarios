@extends('layouts.auth')

@section('content')
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="card o-hidden border-0 shadow-lg my-5 rounded-4" style="max-width: 800px; width: 100%;">
            <div class="card-body p-0">
                <div class="row">
                    <!-- Sección de la imagen -->
                    <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-light rounded-start-4">
                        <img src="{{ asset('img/logo2.png') }}" alt="Imagen decorativa" class="img-fluid"
                             style="max-height: 300px; width: auto;">
                    </div>

                    <!-- Sección del formulario -->
                    <div class="col-lg-6">
                        <div class="p-5">
                            <div class="text-center mb-4">
                                <h1 class="h4 text-gray-900">Restablecer Contraseña</h1>
                            </div>

                            {{-- Mensaje de éxito --}}
                            @if (session('status') || session('success'))
                                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                    {{ session('status') ?? session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Errores de validación --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form class="user" action="{{ route('password.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="form-group mb-3">
                                    <input type="email"
                                           class="form-control form-control-user @error('email') is-invalid @enderror"
                                           name="email"
                                           placeholder="Correo Electrónico"
                                           value="{{ old('email') }}"
                                           required
                                           autocomplete="email">
                                    @error('email')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <input type="password"
                                           class="form-control form-control-user @error('password') is-invalid @enderror"
                                           name="password"
                                           placeholder="Nueva Contraseña"
                                           required
                                           autocomplete="new-password">
                                    @error('password')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <input type="password"
                                           class="form-control form-control-user"
                                           name="password_confirmation"
                                           placeholder="Confirmar Contraseña"
                                           required
                                           autocomplete="new-password">
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block rounded-pill">
                                    Restablecer Contraseña
                                </button>
                            </form>

                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('login') }}">Regresar al inicio de sesión</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="{{ route('register') }}">¿No tienes una cuenta? Regístrate aquí</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
