<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\InventarioFisico; // 🔹 Importamos el modelo de InventarioFisico
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function dashboardResguardante()
    {
        $user = Auth::user();

        // Verificar que el usuario tenga un resguardante vinculado
        if (!$user->resguardante) {
            return redirect()->route('dashboard')->with('error', 'No tienes bienes asignados.');
        }

        // Obtener bienes asignados al resguardante autenticado
        $bienes = Bien::where('resguardante_id', $user->resguardante->id_resguardante)->get();

        // 📌 Obtener el inventario físico más reciente con estado "Programado"
        $inventario = InventarioFisico::where('estado', 'Programado')->latest()->first();

        return view('resguardante.dashboard', compact('bienes', 'inventario'));
    }

    public function misBienes(Request $request)
    {
        $user = Auth::user();

        if (!$user->resguardante) {
            return view('resguardante.bienes')->with('mensaje', 'No tienes un resguardante asignado. Por favor, contacta al administrador.');
        }

        $query = Bien::where('resguardante_id', $user->resguardante->id_resguardante);

        if ($request->filled('term')) {
            $term = $request->term;
            $query->where(function ($q) use ($term) {
                $q->where('numero_inventario', 'like', "%$term%")
                    ->orWhere('numero_serie', 'like', "%$term%")
                    ->orWhere('nombre', 'like', "%$term%");
            });
        }

        $bienes = $query->orderBy('fecha_adquisicion', 'desc')->paginate(8);

        return view('resguardante.bienes', compact('bienes'));
    }


    public function buscarBienesAsignados(Request $request)
    {
        $term = $request->input('term');
        $user = Auth::user();

        if (!$user->resguardante || !$term) {
            return response()->json([]);
        }

        $bienes = Bien::where('resguardante_id', $user->resguardante->id_resguardante)
            ->where(function ($query) use ($term) {
                $query->where('nombre', 'like', "%{$term}%")
                    ->orWhere('numero_inventario', 'like', "%{$term}%")
                    ->orWhere('numero_serie', 'like', "%{$term}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($bien) {
                return [
                    'id' => $bien->id_bien,
                    'nombre' => $bien->nombre,
                    'inventario' => $bien->numero_inventario,
                    'estado' => ucfirst($bien->estado),
                ];
            });

        return response()->json($bienes);
    }





    public function show($id)
    {
        $user = Auth::user();

        // Verificar que el usuario tenga un resguardante asignado
        if (!$user->resguardante) {
            return redirect()->route('resguardante.dashboard')->with('error', 'No tienes bienes asignados.');
        }

        // Buscar el bien solo si pertenece al resguardante autenticado
        $bien = Bien::where('resguardante_id', $user->resguardante->id_resguardante)
            ->where('id_bien', $id)
            ->first();

        if (!$bien) {
            return redirect()->route('resguardante.mis-bienes')->with('error', 'No tienes permiso para ver este bien.');
        }

        return view('resguardante.show', compact('bien'));
    }
}
