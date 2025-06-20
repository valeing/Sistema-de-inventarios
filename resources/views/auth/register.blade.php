@extends('layouts.auth')

@section('content')
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="card o-hidden border-0 shadow-lg my-5" style="max-width: 800px; width: 100%;">
            <div class="card-body p-0">
                <div class="row">
                    <!-- Sección de la imagen -->
                    <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center"
                        style="background-size: cover; background-position: center;">
                        <!-- Aquí puedes colocar una imagen si deseas -->
                        <img src="{{ asset('img/logo2.png') }}" alt="Imagen decorativa" class="img-fluid"
                            style="max-height: 300px; width: auto;">
                    </div>
                    <!-- Sección del formulario -->
                    <div class="col-lg-6">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Registrarse</h1>
                            </div>
                            <form class="user" action="{{ route('register') }}" method="POST">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-sm-12 mb-3">
                                        <input type="text" class="form-control form-control-user" name="name"
                                            placeholder="Nombre Completo" required autocomplete="name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" name="email"
                                        placeholder="Correo Electrónico" required autocomplete="email">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user" name="password"
                                            placeholder="Contraseña" required autocomplete="new-password">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user"
                                            name="password_confirmation" placeholder="Confirmar Contraseña" required
                                            autocomplete="new-password">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Registrarse
                                </button>
                            </form>

                            @if (session('message'))
                                <div class="alert alert-info mt-3">
                                    {{ session('message') }}
                                </div>
                            @endif
                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('login') }}">¿Ya tienes una cuenta? Inicia sesión aquí</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
