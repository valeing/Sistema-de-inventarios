<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReporteBien;
use App\Models\Bien;
use App\Models\Resguardante;
use App\Models\HistorialReporte;
use Barryvdh\DomPDF\Facade\Pdf;


class ReporteBienController extends Controller
{

    public function descargar($id)
    {
        $reporte = ReporteBien::findOrFail($id);
        $bien = $reporte->bien;

        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        // Usa una vista común o diferente si lo deseas
        $view = $prefix . '.reportes.detalles_pdf'; // ej: admin.reportes.detalles_pdf

        $pdf = Pdf::loadView($view, compact('reporte', 'bien'));
        return $pdf->download('Reporte_Bien_' . $reporte->id . '.pdf');
    }
    public function descargarPDF($id)
    {
        $reporte = HistorialReporte::with('bien')->findOrFail($id);
        $bien = $reporte->bien;

        $view = 'resguardante.reportes.detalles_pdf';

        $pdf = Pdf::loadView($view, compact('reporte', 'bien'));
        return $pdf->download('Reporte_Eliminado_' . $reporte->id . '.pdf');
    }


    public function descargarDesdeHistorial($id)
    {
        $reporte = HistorialReporte::with('bien', 'resguardante')->findOrFail($id);
        $bien = $reporte->bien;

        // Obtener el prefijo según el rol
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        // Vista PDF (puedes compartir la misma que con ReporteBien)
        $view = $prefix . '.reportes.detalles_pdf'; // Ej: admin.reportes.detalles_pdf

        // Generar el PDF con DomPDF
        $pdf = Pdf::loadView($view, compact('reporte', 'bien'));

        return $pdf->download('Reporte_Eliminado_' . $reporte->id . '.pdf');
    }




    public function create()
    {
        $resguardante = Resguardante::where('user_id', auth()->id())->first();

        if (!$resguardante) {
            // Enviar mensaje de advertencia a la vista si no tiene resguardante
            $mensajeError = 'No tienes un resguardante asignado. Contacta al administrador del sistema.';
            return view('resguardante.reportes.crear', compact('mensajeError'));
        }

        $bienes = Bien::where('resguardante_id', $resguardante->id_resguardante)->get();

        return view('resguardante.reportes.crear', compact('bienes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bien_id' => 'required|exists:bienes,id_bien',
            'comentario' => 'required|string',
        ]);

        $resguardante = Resguardante::where('user_id', auth()->id())->first();

        ReporteBien::create([
            'resguardante_id' => $resguardante->id_resguardante,
            'bien_id' => $request->bien_id,
            'comentario' => $request->comentario,
            'fecha_reporte' => now(),
        ]);

        return redirect()->route('reportes.mis')->with('success', 'Reporte enviado correctamente.');
    }

    public function misReportes()
    {
        $resguardante = Resguardante::where('user_id', auth()->id())->first();

        if (!$resguardante) {
            $mensajeError = 'No tienes un perfil de resguardante asignado. Contacta al administrador del sistema.';
            return view('resguardante.reportes.mis_reportes', compact('mensajeError'));
        }

        $query = ReporteBien::where('resguardante_id', $resguardante->id_resguardante)->with('bien');

        if (request()->has('bien_id')) {
            $query->where('bien_id', request('bien_id'));
        }

        $reportes = $query->orderBy('fecha_reporte', 'desc')->paginate(10);

        return view('resguardante.reportes.mis_reportes', compact('reportes'));
    }

    public function buscarBienMisReportes()
    {
        $resguardante = Resguardante::where('user_id', auth()->id())->first();

        $term = request('term');

        $bienes = ReporteBien::where('resguardante_id', $resguardante->id_resguardante)
            ->whereHas('bien', function ($query) use ($term) {
                $query->where('nombre', 'like', "%$term%")
                    ->orWhere('numero_inventario', 'like', "%$term%");
            })
            ->with('bien')
            ->limit(5)
            ->get()
            ->map(function ($reporte) {
                return [
                    'id' => $reporte->bien->id_bien,
                    'nombre' => $reporte->bien->nombre,
                    'inventario' => $reporte->bien->numero_inventario
                ];
            });

        return response()->json($bienes);
    }




    public function cancelar($id)
    {
        $reporte = ReporteBien::findOrFail($id);
        $resguardante = Resguardante::where('user_id', auth()->id())->first();

        if ($reporte->resguardante_id == $resguardante->id_resguardante && $reporte->estatus === 'en proceso') {
            $reporte->update(['estatus' => 'cancelado']);
        }

        return redirect()->route('reportes.mis');
    }

    public function index(Request $request)
    {
        $query = ReporteBien::with('resguardante', 'bien')->orderBy('fecha_reporte', 'desc');

        // Si se proporciona un resguardante_id, filtramos
        if ($request->filled('resguardante_id')) {
            $query->where('resguardante_id', $request->resguardante_id);
        }

        $reportes = $query->paginate(10);
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        return view("$prefix.reportes.index", compact('reportes'));
    }


    public function buscarResguardantes(Request $request)
    {
        $term = $request->term;

        $resguardantes = Resguardante::where('nombre_apellido', 'LIKE', "%$term%")
            ->select('id_resguardante', 'nombre_apellido')
            ->limit(5)
            ->get();

        return response()->json($resguardantes);
    }


    public function verPorResguardante($id)
    {
        $resguardante = Resguardante::findOrFail($id);
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $query = ReporteBien::with('bien')
            ->where('resguardante_id', $id)
            ->orderBy('fecha_reporte', 'desc');

        if ($bien = request('bien')) {
            $query->whereHas('bien', function ($q) use ($bien) {
                $q->where('numero_inventario', $bien);
            });
        }

        $reportes = $query->paginate(10);

        return view("$prefix.reportes.lista_por_resguardante", compact('resguardante', 'reportes', 'prefix'));
    }

    public function buscarBienesPorResguardante($id)
    {
        $term = request('term');
        $bienes = ReporteBien::with('bien')
            ->where('resguardante_id', $id)
            ->whereHas('bien', function ($query) use ($term) {
                $query->where('nombre', 'like', "%{$term}%")
                    ->orWhere('numero_inventario', 'like', "%{$term}%");
            })
            ->get();

        $resultados = $bienes->map(function ($reporte) {
            return [
                'id' => $reporte->id,
                'nombre' => $reporte->bien->nombre,
                'inventario' => $reporte->bien->numero_inventario,
            ];
        });

        return response()->json($resultados);
    }


    public function updateEstado(Request $request, $id)
    {
        $request->validate([
            'estatus' => 'required|in:en proceso,completado,rechazado',
            'comentario_rechazo' => 'nullable|string|max:1000',
        ]);

        $reporte = ReporteBien::findOrFail($id);
        $reporte->estatus = $request->estatus;

        if ($request->estatus === 'rechazado') {
            $reporte->comentario_rechazo = $request->comentario_rechazo;
        }

        $reporte->save();

        return back()->with('success', 'Estado actualizado correctamente.');
    }


    public function destroy($id)
    {
        $reporte = ReporteBien::findOrFail($id);

        if (!in_array($reporte->estatus, ['rechazado', 'completado'])) {
            return back()->with('error', 'Solo puedes eliminar reportes rechazados o completados.');
        }

        HistorialReporte::create([
            'reporte_id' => $reporte->id,
            'resguardante_id' => $reporte->resguardante_id,
            'bien_id' => $reporte->bien_id,
            'comentario' => $reporte->comentario,
            'fecha_reporte' => $reporte->fecha_reporte,
            'estatus' => $reporte->estatus,
            'fecha_eliminacion' => now(),
            'comentario_rechazo' => $reporte->comentario_rechazo,
        ]);

        $reporte->delete();

        return redirect()->route('reportes.mis')->with('success', 'Reporte eliminado correctamente. Se ha enviado al historial.');
    }

    public function historial(Request $request)
    {
        $resguardante = Resguardante::where('user_id', auth()->id())->first();

        if (!$resguardante) {
            $mensajeError = 'No tienes un perfil de resguardante asignado. Contacta al administrador del sistema.';
            return view('resguardante.reportes.historial', compact('mensajeError'));
        }

        $query = HistorialReporte::where('resguardante_id', $resguardante->id_resguardante)->with('bien');

        if ($request->has('bien_id')) {
            $query->where('bien_id', $request->bien_id);
        }

        $historial = $query->orderByDesc('fecha_eliminacion')->paginate(10);

        return view('resguardante.reportes.historial', compact('historial'));
    }

    public function buscarBienesHistorial()
    {
        $resguardante = Resguardante::where('user_id', auth()->id())->first();
        $term = request('term');

        $bienes = HistorialReporte::where('resguardante_id', $resguardante->id_resguardante)
            ->whereHas('bien', function ($query) use ($term) {
                $query->where('nombre', 'like', "%$term%")
                    ->orWhere('numero_inventario', 'like', "%$term%");
            })
            ->with('bien')
            ->limit(5)
            ->get()
            ->map(function ($reporte) {
                return [
                    'id' => $reporte->bien->id_bien,
                    'nombre' => $reporte->bien->nombre,
                    'inventario' => $reporte->bien->numero_inventario
                ];
            });

        return response()->json($bienes);
    }








    public function verHistorialPorResguardantes(Request $request)
    {
        $query = Resguardante::whereHas('reportesHistorial');

        if ($request->has('resguardante')) {
            $query->where('nombre_apellido', 'LIKE', '%' . $request->resguardante . '%');
        }

        $resguardantes = $query->withCount('reportesHistorial')->paginate(10);
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        return view("$prefix.reportes.historial.index", compact('resguardantes', 'prefix'));
    }

    public function buscarResguardantesHistorial(Request $request)
    {
        $term = $request->term;

        $resguardantes = Resguardante::whereHas('reportesHistorial')
            ->where('nombre_apellido', 'LIKE', "%{$term}%")
            ->select('id_resguardante', 'nombre_apellido')
            ->get();

        return response()->json($resguardantes);
    }




    public function verHistorialIndividual($resguardanteId)
    {
        $resguardante = Resguardante::findOrFail($resguardanteId);
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $query = HistorialReporte::where('resguardante_id', $resguardanteId)->with('bien');

        // Si viene el parámetro bien_id, filtrar por ese bien
        if (request()->has('bien_id')) {
            $query->where('bien_id', request('bien_id'));
        }

        $historial = $query->orderByDesc('fecha_eliminacion')->paginate(10);

        return view("$prefix.reportes.historial.lista_por_resguardante", compact('historial', 'resguardante', 'prefix'));
    }

    public function buscarBienHistorial($resguardanteId)
    {
        $term = request('term');

        $bienes = HistorialReporte::with('bien')
            ->where('resguardante_id', $resguardanteId)
            ->whereHas('bien', function ($query) use ($term) {
                $query->where('nombre', 'like', "%$term%")
                    ->orWhere('numero_inventario', 'like', "%$term%");
            })
            ->limit(5)
            ->get()
            ->map(function ($reporte) {
                return [
                    'id' => $reporte->bien->id_bien,
                    'nombre' => $reporte->bien->nombre,
                    'inventario' => $reporte->bien->numero_inventario
                ];
            });

        return response()->json($bienes);
    }

    public function buscarBienEliminado(Request $request, $resguardanteId)
    {
        $termino = $request->get('term');

        $resultados = HistorialReporte::where('resguardante_id', $resguardanteId)
            ->whereHas('bien', function ($query) use ($termino) {
                $query->where('nombre', 'LIKE', "%$termino%")
                    ->orWhere('numero_inventario', 'LIKE', "%$termino%");
            })
            ->with('bien')
            ->take(5)
            ->get();

        $sugerencias = $resultados->map(function ($reporte) {
            return [
                'id' => $reporte->id,
                'nombre' => $reporte->bien->nombre ?? '',
                'inventario' => $reporte->bien->numero_inventario ?? ''
            ];
        });

        return response()->json($sugerencias);
    }



    public function eliminarDefinitivo($id)
    {
        $reporte = HistorialReporte::findOrFail($id);
        $reporte->delete();

        return back()->with('success', 'Reporte eliminado definitivamente del historial.');
    }



    public function buscarBienes(Request $request)
    {
        $resguardante = Resguardante::where('user_id', auth()->id())->first();

        $query = Bien::where('resguardante_id', $resguardante->id_resguardante);

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                    ->orWhere('numero_inventario', 'like', '%' . $request->search . '%');
            });
        }

        // Total sin filtrar (para recordsTotal)
        $total = Bien::where('resguardante_id', $resguardante->id_resguardante)->count();

        // Total filtrado
        $filtered = $query->count();

        // Paginación
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $draw = $request->input('draw');

        $bienes = $query->skip($start)->take($length)->get()->map(function ($bien) {
            return [
                'id_bien' => $bien->id_bien,
                'nombre' => $bien->nombre,
                'numero_inventario' => $bien->numero_inventario,
                'estado' => $bien->estado,
                'imagen_url' => $bien->imagen
                    ? 'data:' . $bien->mime_type . ';base64,' . $bien->imagen
                    : 'https://via.placeholder.com/50',
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $bienes
        ]);
    }



}
