<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\ResguardanteController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\DirectController;
use App\Http\Controllers\ExportarTodosBienesController;
use App\Http\Controllers\ExportarBienesController;
use App\Http\Controllers\BajasController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ImportBienesController;
use App\Http\Controllers\Auth\CustomResetPasswordController;
use App\Http\Controllers\InventarioFisicoController;
use App\Models\InventarioFisico;
use App\Http\Controllers\ReporteBienController;

// ✅ Ruta principal
Route::redirect('/', '/login');



// 🔹 **Rutas públicas (Sin autenticación)**
Route::middleware('guest')->group(function () {
    Route::view('/register', 'auth.register')->name('register');
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/bienes/publico/{id}', [BienController::class, 'verPublico'])->name('bienes.public_show');

    Route::get('password/reset/{token}', [CustomResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('password/reset', [CustomResetPasswordController::class, 'reset'])
        ->name('password.update');

});

// 🔹 **Rutas protegidas (Usuarios autenticados)**
Route::middleware('auth')->group(function () {

    // ✔️ **Cerrar sesión**
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/')->with('message', 'Sesión cerrada correctamente.');
    })->name('logout');

    // ✔️ **Perfil de usuario (Común para todos los roles)**
    Route::prefix('perfil')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    });

    // ✔️ **Rutas Compartidas para Administrador y Operador**
    foreach (['admin', 'operador'] as $role) {
        Route::prefix($role)->middleware("role:" . ucfirst($role))->group(function () use ($role) {

            Route::get('/dashboard', [DashboardController::class, 'index'])->name("$role.dashboard");
            Route::redirect('/', "/$role/dashboard");

            // ✅ **Notificaciones**
            Route::prefix('notifications')->name("$role.notifications.")->group(function () {
                Route::get('/', [NotificationController::class, 'index'])->name('index');
                Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
                Route::patch('/{id}/mark-as-seen', [NotificationController::class, 'markAsSeen'])->name('markAsSeen');
                Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
                Route::delete('/{email}/destroy-all', [NotificationController::class, 'destroyAll'])->name('destroyAll');
                Route::patch('/{email}/mark-all-as-seen', [NotificationController::class, 'markAllAsSeen'])->name('markAllAsSeen');
            });
            Route::prefix('perfil')->name("$role.profile.")->group(function () use ($role) {
                Route::get('/', [ProfileController::class, 'show'])->name('show');
                Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('updatePassword');
            });

            // ✅ **Gestión de usuarios**
            Route::prefix('users')->name("$role.users.")->group(function () {
                Route::get('/', [AuthController::class, 'index'])->name('index');
                Route::get('/all', [AuthController::class, 'getAllUsers'])->name('all');
                Route::get('/register', [AuthController::class, 'create'])->name('create');
                Route::post('/register', [AuthController::class, 'store'])->name('store');
                Route::get('/users/search', [AuthController::class, 'search'])->name('search');
                Route::get('/buscar-resguardante', [AuthController::class, 'buscarResguardante'])->name('buscar.resguardante');
                Route::get('/{user}', [AuthController::class, 'show'])->name('show');
                Route::get('/{user}/edit', [AuthController::class, 'edit'])->name('edit');
                Route::put('/{user}', [AuthController::class, 'update'])->name('update');
                Route::delete('/{user}', [AuthController::class, 'destroy'])->name('destroy');
                Route::delete('/{user}/desvincular', [AuthController::class, 'desvincularResguardante'])->name('desvincular');
            });

            // ✅ **Gestión de bienes**
            Route::prefix('bienes')->name("$role.bienes.")->group(function () {
                Route::get('/', [BienController::class, 'index'])->name('index');
                Route::get('/crear', [BienController::class, 'create'])->name('create');
                Route::post('/guardar', [BienController::class, 'store'])->name('store');
                Route::get('/{id}/ver', [BienController::class, 'show'])->name('show');
                Route::get('/{id}/editar', [BienController::class, 'edit'])->name('edit');
                Route::put('/{id}/actualizar', [BienController::class, 'update'])->name('update');
                Route::delete('/{id}/eliminar', [BienController::class, 'destroy'])->name('destroy');
                Route::get('/{id}/pdf', [BienController::class, 'generatePDF'])->name('pdf');

                Route::post('/descargar-etiquetas', [BienController::class, 'descargarEtiquetasPDF'])->name('descargar_etiquetas');
                Route::get('/exportar-todos', [ExportarTodosBienesController::class, 'exportar'])->name('exportar.todos');
                Route::post('/exportar-bienes', [ExportarBienesController::class, 'exportar'])->name('exportar.bienes');
                Route::post('/importar-bienes', [ImportBienesController::class, 'importar'])->name('importar.bienes');
            });

            // ✅ **Gestión de resguardantes**
            Route::prefix('resguardantes')->name("$role.resguardantes.")->group(function () {
                Route::get('/', [ResguardanteController::class, 'index'])->name('index');
                Route::get('/crear', [ResguardanteController::class, 'create'])->name('create');
                Route::get('/all', [ResguardanteController::class, 'getAllResguardantes'])->name('all');
                Route::post('/guardar', [ResguardanteController::class, 'store'])->name('store');
                Route::get('/{id}/ver', [ResguardanteController::class, 'show'])->name('show');
                Route::get('/{id}/editar', [ResguardanteController::class, 'edit'])->name('edit');
                Route::put('/{id}/actualizar', [ResguardanteController::class, 'update'])->name('update');
                Route::delete('/{id}/eliminar', [ResguardanteController::class, 'destroy'])->name('destroy');
                Route::post('/descargar-etiquetas', [BienController::class, 'descargarEtiquetasPDF'])->name('descargar_etiquetas');
            });

            // ✅ **Gestión de asignaciones**
            Route::prefix('asignaciones')->name("$role.asignaciones.")->group(function () {
                Route::get('/', [AsignacionController::class, 'index'])->name('index'); // Listado principal
                Route::get('/crear', [AsignacionController::class, 'create'])->name('create'); // Formulario de asignación
                Route::post('/guardar', [AsignacionController::class, 'store'])->name('store'); // Guardar asignación
                Route::get('/{id}/ver', [AsignacionController::class, 'show'])->name('show'); // Ver bienes asignados a un resguardante
                Route::get('/asignaciones/{id}/editar', [AsignacionController::class, 'edit'])->name('edit'); // 💡 Aquí está la ruta de editar
                Route::put('/{id}/actualizar', [AsignacionController::class, 'update'])->name('update'); // Actualizar asignación
                Route::delete('/{id}/eliminar', [AsignacionController::class, 'destroy'])->name('destroy'); // Eliminar asignación
                Route::get('/{id}/editar', [AsignacionController::class, 'edit'])->name('asignaciones.edit');
                Route::get('/get-bienes', [AsignacionController::class, 'getBienes'])->name('get-bienes');
                Route::delete('/resguardante/{id}/desasignar', [AsignacionController::class, 'desasignarTodos'])
                    ->name('desasignarTodos');
                Route::delete('/bien/{id}/desasignar', [AsignacionController::class, 'desasignarIndividual'])
                    ->name('desasignarIndividual');
            });
            // ✅ **Gestión de direcciones y departamentos (Direct)**
            Route::prefix('direct')->name("$role.direct.")->group(function () {
                Route::get('/', [DirectController::class, 'index'])->name('index');
                Route::get('/crear', [DirectController::class, 'create'])->name('create');
                Route::post('/guardar', [DirectController::class, 'store'])->name('store');
                Route::get('/{id}/ver', [DirectController::class, 'show'])->name('show');
                Route::get('/{id}/editar', [DirectController::class, 'edit'])->name('edit');
                Route::put('/{id}/actualizar', [DirectController::class, 'update'])->name('update');
                Route::delete('/{id}/eliminar', [DirectController::class, 'destroy'])->name('destroy');
                Route::delete('/departamento/{id}/eliminar', [DirectController::class, 'destroyDepartamento'])->name('destroyDepartamento');
            });

            // ✅ **Gestión de bajas**
            Route::prefix('bajas')->name("$role.bajas.")->group(function () {
                Route::get('/', [BajasController::class, 'index'])->name('index'); // Historial de bajas
                Route::get('/all', [BajasController::class, 'getAllBajas'])->name('all'); // Obtener todas las bajas
                Route::get('/crear', [BajasController::class, 'create'])->name('create'); // Formulario de baja
                Route::post('/guardar', [BajasController::class, 'store'])->name('store'); // Guardar baja
                Route::get('/buscar', [BajasController::class, 'buscar'])->name('buscar'); // 🔹 Se corrige el error de doble "bajas"
                Route::get('/listar-bienes', [BajasController::class, 'listarBienesInactivos'])->name('listar-bienes'); // ✅ Carga bienes inactivos// Rutas con {id} (evitar conflictos)
                Route::get('/{id}/ver', [BajasController::class, 'show'])->name('show'); // Ver detalles
                Route::get('/{id}/descargar', [BajasController::class, 'downloadExpediente'])->name('download'); // Descargar PDF
                Route::get('/{id}/exportar', [BajasController::class, 'export'])->name('export'); // Exportar PDF
            });

            // ✅ **Gestión de inventario físico**
            Route::prefix('inventario_fisico')->name("$role.inventario_fisico.")->group(function () {
                Route::get('/', [InventarioFisicoController::class, 'index'])->name('index');
                Route::get('/crear', [InventarioFisicoController::class, 'create'])->name('create');
                Route::post('/guardar', [InventarioFisicoController::class, 'store'])->name('store');
                Route::get('/{id}/ver', [InventarioFisicoController::class, 'show'])->name('show');
                Route::get('/{id}/editar', [InventarioFisicoController::class, 'edit'])->name('edit');
                Route::put('/{id}/actualizar', [InventarioFisicoController::class, 'update'])->name('update');
                Route::delete('/{id}/eliminar', [InventarioFisicoController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('reportes')->name("$role.reportes.")->group(function () {
                Route::get('/', [ReporteBienController::class, 'index'])->name('index'); // Lista general

                Route::get('/{resguardante}/ver', [ReporteBienController::class, 'verPorResguardante'])->name('verPorResguardante'); // Ver reportes de 1 resguardante

                Route::put('/{id}/actualizar', [ReporteBienController::class, 'updateEstado'])->name('update'); // Cambiar estado

                // Nueva ruta para descargar PDF del reporte y bien
                Route::get('/{id}/descargar', [ReporteBienController::class, 'descargar'])->name('descargar');
                Route::get('/{id}/buscar-bien', [ReporteBienController::class, 'buscarBienesPorResguardante'])->name('buscarBienes');
                // Historial: lista de resguardantes con reportes eliminados
                Route::get('/historial', [ReporteBienController::class, 'verHistorialPorResguardantes'])->name('historial.index');

                // Ver reportes eliminados de un resguardante (ruta renombrada)
                Route::get('/historial/{resguardante}/ver', [ReporteBienController::class, 'verHistorialIndividual'])->name('historial.ver');

                // Eliminar permanentemente un reporte del historial
                Route::delete('/historial/{id}/eliminar', [ReporteBienController::class, 'eliminarDefinitivo'])->name('historial.eliminar');
                Route::get('/historial/{id}/descargar', [ReporteBienController::class, 'descargarDesdeHistorial'])->name('historial.descargar');
                // Ruta para búsqueda AJAX de resguardantes
                Route::get('/buscar-resguardantes', [ReporteBienController::class, 'buscarResguardantes'])->name('buscarResguardantes');
                Route::get('/historial/buscar-resguardantes', [ReporteBienController::class, 'buscarResguardantesHistorial'])->name('historial.buscar');
                Route::get('/historial/{resguardante}/buscar-bien', [ReporteBienController::class, 'buscarBienHistorial']);





            });


        });
    }


    // ✅ *Rutas para los resguardantes*
    Route::prefix('resguardante')->middleware('role:Resguardante')->group(function () {
        Route::view('/', 'resguardante.dashboard')->name('resguardante.dashboard');
        Route::get('/mis-bienes', [UserDashboardController::class, 'misBienes'])->name('resguardante.mis-bienes');

        // Ver detalles de un bien específico
        Route::get('/mis-bienes/{id}', [UserDashboardController::class, 'show'])->name('resguardante.bienes.show');
        Route::get('/bienes/{id}/pdf', [BienController::class, 'generatePDF'])->name('bienes.pdf');
        Route::get('/reportes/crear', [ReporteBienController::class, 'create'])->name('reportes.create');
        Route::post('/reportes', [ReporteBienController::class, 'store'])->name('reportes.store');
        Route::get('/reportes/mis-reportes', [ReporteBienController::class, 'misReportes'])->name('reportes.mis');
        Route::put('/reportes/cancelar/{id}', [ReporteBienController::class, 'cancelar'])->name('reportes.cancelar');
        Route::delete('/reportes/{id}/eliminar', [ReporteBienController::class, 'destroy'])->name('reportes.eliminar');
        Route::get('/reportes/historial', [ReporteBienController::class, 'historial'])->name('reportes.historial');
        Route::get('reportes/historial', [ReporteBienController::class, 'historial'])->name('reportes.historial');
        Route::get('/bienes/buscar', [ReporteBienController::class, 'buscarBienes'])->name('reportes.buscar');
        Route::get('/reportes/historial/{id}/descargar', [ReporteBienController::class, 'descargarPDF'])->name('resguardante.reportes.historial.descargar');
        Route::get('/reportes/buscar-bien', [ReporteBienController::class, 'buscarBienMisReportes']);
        Route::get('/reportes/buscar-bien-historial', [ReporteBienController::class, 'buscarBienesHistorial'])->name('resguardante.reportes.buscarBienesHistorial');
        Route::get('/resguardante/bienes/buscar', [UserDashboardController::class, 'buscarBienesAsignados'])
            ->name('resguardante.bienes.buscar');


    });




    // ✅ **Otras funciones**
    Route::get('/departamentos/{id_direccion}', [ResguardanteController::class, 'getDepartamentos']);
    Route::get('/buscar-resguardantes', [AsignacionController::class, 'buscarResguardantes'])->name('buscar.resguardantes');
    Route::get('/administrador/bienes/buscar', [AsignacionController::class, 'buscarBienes']);


});

// 🔹 **Rutas para recuperación de contraseñas**
Route::get('forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
Route::get('/api/inventario/ultimo', function () {
    $inventario = InventarioFisico::where('estado', 'Programado')->latest()->first();

    if (!$inventario) {
        return response()->json([
            'message' => 'No hay avisos por el momento'
        ]);
    }

    return response()->json([
        'nombre' => $inventario->nombre_inventario,
        'descripcion' => $inventario->descripcion,
        'fecha_inicio' => \Carbon\Carbon::parse($inventario->fecha_inicio)->format('d/m/Y'),
        'fecha_fin' => \Carbon\Carbon::parse($inventario->fecha_finalizacion)->format('d/m/Y'),
        'comentario' => !empty($inventario->comentario) ? $inventario->comentario : 'Sin comentarios',
    ]);
})->name('api.inventario.ultimo');

