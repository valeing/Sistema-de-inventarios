@extends('layouts.auth')

@section('content')
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="card o-hidden border-0 shadow-lg my-5" style="max-width: 800px; width: 100%;">
            <div class="card-body p-0">
                <div class="row">
                    <!-- Sección de la imagen -->
                    <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center">
                        <img src="{{ asset('img/logo2.png') }}" alt="Imagen decorativa" class="img-fluid"
                            style="max-height: 300px; width: auto;">
                    </div>
                    <!-- Sección del formulario -->
                    <div class="col-lg-6">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Iniciar sesión</h1>
                            </div>

                            <form class="user" action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" name="email"
                                        placeholder="Correo Electrónico" required autocomplete="username">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" name="password"
                                        placeholder="Contraseña" required autocomplete="current-password">
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Iniciar Sesión
                                </button>

                                @if ($errors->any())
                                    <div class="alert alert-danger mt-3">
                                        {{ $errors->first() }}
                                    </div>
                                @endif
                            </form>


                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
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
