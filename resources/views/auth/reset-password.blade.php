@extends('layouts.auth')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card o-hidden border-0 shadow-lg my-5" style="max-width: 800px; width: 100%;">
        <div class="card-body p-0">
            <div class="row">
                <!-- Sección de la imagen -->
                <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center">
                    <img src="{{ asset('img/logo2.png') }}" alt="Imagen decorativa" class="img-fluid" style="max-height: 300px; width: auto;">
                </div>
                <!-- Sección del formulario -->
                <div class="col-lg-6">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Restablecer Contraseña</h1>
                        </div>

                        {{-- Mensajes de éxito/error --}}
                        @if (session('status') || session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            {{ session('status') ?? session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group">
                                <input type="email"
                                    class="form-control form-control-user @error('email') is-invalid @enderror"
                                    name="email"
                                    placeholder="Correo Electrónico"
                                    value="{{ old('email') }}"
                                    required>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input type="password"
                                    class="form-control form-control-user @error('password') is-invalid @enderror"
                                    name="password"
                                    placeholder="Nueva Contraseña"
                                    required>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input type="password"
                                    class="form-control form-control-user"
                                    name="password_confirmation"
                                    placeholder="Confirmar Contraseña"
                                    required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-user btn-block">Restablecer Contraseña</button>
                        </form>

                        <hr>
                        <div class="text-center">
                            <a class="small" href="{{ route('login') }}">Regresar al inicio de sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
