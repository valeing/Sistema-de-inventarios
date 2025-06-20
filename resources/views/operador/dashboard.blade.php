@extends(Auth::user()->role->name === 'Administrador' ? 'layouts.admin-layout' : 'layouts.operador-layout')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="container mt-4">
            <h1 class="h3 mb-4 text-gray-800">
                Bienvenido al panel del
                {{ Auth::user()->role->name === 'Administrador' ? 'Administración' : 'Operador' }}
            </h1>

            <!-- Mensaje de Bienvenida -->
            <div class="alert alert-primary">
                <h4>Bienvenido, {{ auth()->user()->name }} 👋</h4>
                <p>Hoy es {{ date('d/m/Y') }}. ¡Esperamos que tengas un excelente día gestionando el sistema!</p>
            </div>

            <div class="row">

                <!-- Total de Bienes -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total de Bienes</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBienes ?? 0 }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resguardantes Registrados -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Resguardantes Registrados</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $resguardantesRegistrados ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bienes Asignados -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Bienes Asignados</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bienesAsignados ?? 0 }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-handshake fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bienes sin asignar -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Bienes sin Asignar</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $totalBienes - $bienesAsignados }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reportes Activos -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Reportes Activos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalReportesActivos ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total de Inventarios Físicos -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-dark shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                        Inventarios Físicos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inventariosFisicos ?? 0 }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total de Bajas -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-muted shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">
                                        Bajas Totales</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bajasTotales ?? 0 }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-trash-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acceso rápido: Registrar baja de bien -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <a href="{{ route($prefix . '.bajas.create') }}" class="text-decoration-none">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Registrar baja de bien
                                    </div>
                                    <div class="h6 mb-0 text-gray-800">Formulario</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-arrow-alt-circle-down fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>


            </div>

        </div>

    </div>
@endsection
