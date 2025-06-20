<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\Resguardante;
use App\Models\User;
use App\Models\Direccion;
use App\Models\Departamento;
use App\Models\Baja;
use App\Models\InventarioFisico;
use Illuminate\Http\Request;
use App\Models\ReporteBien;
use App\Models\HistorialReporte;


class DashboardController extends Controller
{
    public function index()
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $totalBienes = Bien::count();
        $resguardantesRegistrados = Resguardante::count();
        $bienesAsignados = Bien::whereNotNull('resguardante_id')->count();
        $usuariosRegistrados = User::count();
        $totalDirecciones = Direccion::count();
        $totalDepartamentos = Departamento::count();
        $bajasTotales = Baja::count();
        $inventariosFisicos = InventarioFisico::count();

        $totalReportesActivos = ReporteBien::count();
        $totalReportesEliminados = HistorialReporte::count();

        return view($prefix . '.dashboard', compact(
            'totalBienes',
            'resguardantesRegistrados',
            'bienesAsignados',
            'usuariosRegistrados',
            'totalDirecciones',
            'totalDepartamentos',
            'bajasTotales',
            'inventariosFisicos',
            'totalReportesActivos',
            'totalReportesEliminados',
            'prefix' // también lo pasamos por si la vista lo necesita
        ));
    }
}
