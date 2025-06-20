@php
    $layout = match(Auth::user()->role->name) {
        'Administrador' => 'layouts.admin-layout',
        'Operador' => 'layouts.operador-layout',
        'Resguardante' => 'layouts.resguardante',
        default => 'layouts.default-layout' // En caso de que algún otro rol no definido intente entrar
    };
@endphp

@extends($layout)

@section('content')
    @yield('contenido')
@endsection
