<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'Dashboard')</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">


    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        @php
            $prefix = request()->segment(1); // Detecta si es 'admin' o 'operador'
        @endphp

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center"
                href="{{ route('admin.dashboard') }}">
                <div class="sidebar-brand-icon">
                    <img src="{{ asset('img/upb-logo_0_0.png') }}" alt="Icon" class="img-fluid logo-responsive">
                </div>
            </a>



            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-house"></i>
                    <span>Inicio</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">Interfaz Administrador</div>

            <!-- Nav Item - Bienes -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseComponents1"
                    aria-expanded="true" aria-controls="collapseComponents1">
                    <i class="fa-solid fa-boxes-packing"></i>
                    <span>Bienes</span>
                </a>
                <div id="collapseComponents1" class="collapse" aria-labelledby="headingComponents1"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.bienes.create") }}">
                            Registrar bienes
                        </a>
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.bienes.index") }}">Ver
                            bienes</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Resguardantes -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseComponents2"
                    aria-expanded="true" aria-controls="collapseComponents2">
                    <i class="fa-solid fa-user-group"></i>
                    <span>Resguardantes</span>
                </a>
                <div id="collapseComponents2" class="collapse" aria-labelledby="headingComponents2"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.resguardantes.create") }}">
                            Registrar resguardantes
                        </a>
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.resguardantes.index") }}">
                            Lista de resguardantes
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Gestor de Bienes -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseComponents3"
                    aria-expanded="true" aria-controls="collapseComponents3">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Gestor de bienes</span>
                </a>
                <div id="collapseComponents3" class="collapse" aria-labelledby="headingComponents3"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.asignaciones.create") }}">
                            Asignar bienes
                        </a>
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.asignaciones.index") }}">
                            Lista de asignaciones
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Gestión de Usuarios -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseComponents4"
                    aria-expanded="true" aria-controls="collapseComponents4">
                    <i class="fa-solid fa-user-gear"></i>
                    <span>Gestión de usuarios</span>
                </a>
                <div id="collapseComponents4" class="collapse" aria-labelledby="headingComponents4"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.users.create") }}">
                            Registrar usuarios
                        </a>
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.users.index") }}">
                            Ver lista de usuarios
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Direcciones y Departamentos -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapseComponents5" aria-expanded="true" aria-controls="collapseComponents5">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>Direcciones y departamentos</span>
                </a>
                <div id="collapseComponents5" class="collapse" aria-labelledby="headingComponents5"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.direct.create") }}">
                            Nueva dirección y departamento
                        </a>
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.direct.index") }}">
                            Listado de direcciones
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Baja de Bienes -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapseComponents6" aria-expanded="true" aria-controls="collapseComponents6">
                    <i class="fa-solid fa-cart-arrow-down"></i>
                    <span>Baja de bienes</span>
                </a>
                <div id="collapseComponents6" class="collapse" aria-labelledby="headingComponents6"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.bajas.create") }}">
                            Nueva baja
                        </a>
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.bajas.index") }}">
                            Historial de bajas
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Inventario Físico -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapseComponents7" aria-expanded="true" aria-controls="collapseComponents7">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <span>Inventario Físico</span>
                </a>
                <div id="collapseComponents7" class="collapse" aria-labelledby="headingComponents7"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.inventario_fisico.create") }}">
                            Crear inventario
                        </a>
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.inventario_fisico.index") }}">
                            Ver inventarios
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Reportes -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapseComponents8" aria-expanded="true" aria-controls="collapseComponents8">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <span>Reportes</span>
                </a>
                <div id="collapseComponents8" class="collapse" aria-labelledby="headingComponents8"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.reportes.index") }}">
                            Reportes
                        </a>
                        <a class="collapse-item text-wrap text-truncate d-block"
                            href="{{ route("$prefix.reportes.historial.index") }}">
                            Reportes eliminados
                        </a>

                    </div>
                </div>
            </li>



            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Notificaciones -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Contador de notificaciones -->
                                @php
                                    $unseenNotificationsCount = \App\Models\Notification::where('seen', false)->count();
                                @endphp
                                <span id="notificationCount" class="badge badge-danger badge-counter">
                                    {{ $unseenNotificationsCount }}
                                </span>
                            </a>
                            <!-- Dropdown de notificaciones -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">Centro de Notificaciones</h6>
                                @php
                                    $recentNotifications = \App\Models\Notification::where('seen', false)
                                        ->orderBy('requested_at', 'desc')
                                        ->take(5)
                                        ->get();
                                @endphp
                                <div id="notificationList">
                                    @forelse ($recentNotifications as $notification)
                                        <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route("$prefix.notifications.show", $notification->id) }}">
                                            <div class="font-weight-bold">
                                                <div class="text-truncate">
                                                    {{ $notification->email }} solicitó acceso.
                                                </div>
                                                <div class="small text-gray-500">
                                                    {{ \Carbon\Carbon::parse($notification->requested_at)->format('d-m-Y h:i') }}
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <a class="dropdown-item text-center small text-gray-500" href="#">
                                            No hay nuevas notificaciones
                                        </a>
                                    @endforelse
                                </div>
                                <a class="dropdown-item text-center small text-gray-500"
                                    href="{{ route("$prefix.notifications.index") }}">
                                    Ver todas las notificaciones
                                </a>
                            </div>
                        </li>

                        <!-- Usuario -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ Auth::user()->role->name }} {{ Auth::user()->name }}
                                </span>
                                <img class="img-profile rounded-circle" src="{{ asset('img/undraw_profile.svg') }}">
                            </a>
                            <!-- Dropdown -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route("$prefix.profile.show") }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Perfil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Salir
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <p>&copy; {{ date('Y') }} Universidad Politécnica de Bacalar, All rights reserved.</p>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fa-solid fa-chevron-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <!-- jQuery y DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">


    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const notificationContainer = document.getElementById("notification-container");
            const notificationCount = document.getElementById("notification-count");

            const prefix = "{{ $prefix }}";

            function loadNotifications() {
                fetch(`/${prefix}/notifications`, {
                        method: "GET",
                        credentials: "include",
                        headers: {
                            "Accept": "application/json"
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error HTTP: ${response.status}`);
                        }

                        const contentType = response.headers.get("content-type") || "";
                        if (!contentType.includes("application/json")) {
                            return null;
                        }

                        return response.json();
                    })
                    .then(data => {
                        if (data && Array.isArray(data.notifications)) {
                            notificationContainer.innerHTML = "";
                            notificationCount.textContent = data.notifications.length;

                            if (data.notifications.length === 0) {
                                notificationContainer.innerHTML =
                                    "<span class='dropdown-item'>No hay notificaciones disponibles.</span>";
                            } else {
                                data.notifications.forEach(n => {
                                    const div = document.createElement("div");
                                    div.classList.add("dropdown-item", "text-wrap");
                                    div.innerHTML = `
                                        <strong>${n.mensaje}</strong><br>
                                        <small class="text-muted">${n.fecha}</small>
                                    `;
                                    notificationContainer.appendChild(div);
                                });
                            }
                        }
                    })
                    .catch(() => {
                        // No mostramos detalles de error en consola para seguridad
                        notificationContainer.innerHTML =
                            "<span class='dropdown-item text-danger'>Error al cargar notificaciones.</span>";
                        notificationCount.textContent = "0";
                    });
            }

            function deleteNotification(id) {
                fetch(`/${prefix}/notifications/${id}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                "content"),
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadNotifications();
                        }
                    })
                    .catch(() => {
                        // Silencio el error para evitar mostrar información técnica
                    });
            }

            loadNotifications();
        });
    </script>



</body>

</html>
