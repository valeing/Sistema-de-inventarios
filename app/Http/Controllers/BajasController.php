<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Baja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BajasController extends Controller
{
    /**
     * Mostrar formulario para registrar una nueva baja.
     */
    public function create()
    {
        // Obtener el prefijo desde el contexto de Laravel
        $prefix = app('prefix');

        // Obtener los bienes inactivos
        $bienes = Bien::where('estado', 'inactivo')->get();

        // Pasar el prefijo a la vista
        return view("{$prefix}.bajas.form", compact('bienes', 'prefix'));
    }


    /**
     * Almacenar una nueva baja en la base de datos.
     */
    public function store(Request $request)
    {
        $prefix = app('prefix');

        $request->validate([
            'id_bien' => 'required|exists:bienes,id_bien',
            'motivo' => 'required|string',
            'descripcion_problema' => 'nullable|string',
            'fecha_baja' => 'required|date',
            'expediente' => 'required|file|mimes:pdf|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $bien = Bien::findOrFail($request->id_bien);

            if (!$bien) {
                return back()->withErrors(['error' => 'El bien seleccionado no existe.']);
            }

            // Obtener el nombre completo del resguardante si existe
            $nombreResguardante = $bien->asignacion && $bien->asignacion->resguardante
                ? $bien->asignacion->resguardante->nombre_apellido
                : 'N/A';

            // Guardar el PDF como BLOB
            $expediente = file_get_contents($request->file('expediente')->path());

            // Procesar y recomprimir imagen (si existe)
            $imagenBase64 = null;
            $mimeType = null;

            if ($bien->imagen) {
                $decoded = base64_decode($bien->imagen);
                $image = imagecreatefromstring($decoded);

                if ($image) {
                    ob_start();
                    imagejpeg($image, null, 60); // Calidad ajustada
                    $compressed = ob_get_clean();
                    $imagenBase64 = base64_encode($compressed);
                    $mimeType = $bien->mime_type ?? 'image/jpeg';

                    imagedestroy($image);

                    // Validar tamaño final (por ejemplo, 1 MB)
                    if (strlen($imagenBase64) > 1024 * 1024) {
                        return back()->withErrors(['imagen' => 'La imagen del bien es demasiado grande incluso después de comprimirla.'])->withInput();
                    }
                }
            }

            // Guardar la baja
            $baja = new Baja([
                'id_bien' => $bien->id_bien,
                'motivo' => $request->motivo,
                'descripcion_problema' => $request->descripcion_problema,
                'fecha_baja' => $request->fecha_baja,
                'expediente' => $expediente,
                'mime_type_expediente' => $request->file('expediente')->getClientMimeType(),
                'nombre_apellido' => $nombreResguardante,
                'categoria' => $bien->categoria,
                'estado' => $bien->estado,
                'departamento' => $bien->departamento ? $bien->departamento->nombre : 'Sin departamento',
                'numero_inventario' => $bien->numero_inventario,
                'numero_serie' => $bien->numero_serie,
                'nombre' => $bien->nombre,
                'descripcion_general' => $bien->descripcion_general,
                'observaciones' => $bien->observaciones,
                'fecha_adquisicion' => $bien->fecha_adquisicion,
                'imagen' => $imagenBase64,
                'mime_type' => $mimeType,
            ]);

            $baja->save();
            DB::commit();

            return redirect()->route($prefix . '.bajas.index')
                ->with('success', 'El bien ha sido dado de baja correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar la baja: ' . $e->getMessage()]);
        }
    }



    /**
     * Listar todas las bajas registradas.
     */
    public function index(Request $request)
    {
        $prefix = app('prefix'); // Suponiendo que tienes el prefijo almacenado de alguna forma

        $query = Baja::query();

        // Filtrar por número de inventario, nombre o fecha de baja
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_inventario', 'like', "%$buscar%")
                    ->orWhere('nombre', 'like', "%$buscar%");
            });

            // 🔹 Si la solicitud es AJAX, retornar resultados como JSON
            if ($request->ajax()) {
                return response()->json(
                    $query->limit(5)->get(['id', 'numero_inventario', 'nombre', 'fecha_baja'])
                );
            }
        }

        // Filtrar por fecha seleccionada
        if ($request->filled('fecha_baja')) {
            $fechaFiltro = $request->input('fecha_baja');

            if ($fechaFiltro === 'hoy') {
                $query->whereRaw("DATE(fecha_baja) = CURDATE()");
            } elseif ($fechaFiltro === 'semana') {
                // 🔹 Últimos 7 días
                $query->whereBetween('fecha_baja', [Carbon::today()->subDays(6), Carbon::today()]);
            } elseif ($fechaFiltro === 'mes') {
                // 🔹 Últimos 30 días
                $query->whereBetween('fecha_baja', [Carbon::today()->subDays(29), Carbon::today()]);
            } elseif ($fechaFiltro === 'personalizada' && $request->filled('fecha_personalizada')) {
                // 🔹 Filtrar por fecha exacta personalizada
                $fechaPersonalizada = $request->input('fecha_personalizada');
                $query->whereDate('fecha_baja', $fechaPersonalizada);
            }
        }

        // Ordenar y paginar resultados
        $bajas = $query->orderBy('fecha_baja', 'desc')->paginate(10)->appends($request->query());

        // Utilizando el prefijo en la vista
        return view($prefix . '.bajas.historial', compact('bajas')); // Aquí se agrega el prefijo a la vista
    }




    public function downloadExpediente($id)
    {
        $baja = Baja::findOrFail($id);

        if (!$baja->expediente) {
            return back()->withErrors(['error' => 'El expediente no está disponible.']);
        }

        return response()->streamDownload(function () use ($baja) {
            echo $baja->expediente;
        }, 'Expediente_Baja_' . $baja->id . '.pdf', [
            'Content-Type' => $baja->mime_type_expediente,
            'Content-Length' => strlen($baja->expediente),
        ]);
    }


    public function export($id)
    {
        $baja = Baja::with(['bien', 'bien.asignacion.resguardante'])->findOrFail($id);
        $pdf = PDF::loadView('admin.bajas.pdf', compact('baja'));
        return $pdf->download('Expediente_Baja_' . $baja->id . '.pdf');
    }




    /**
     * Mostrar los detalles de una baja específica.
     */
    public function show($id)
    {
        $prefix = app('prefix'); // Suponiendo que tienes el prefijo almacenado de alguna forma

        $baja = Baja::with(['bien', 'bien.asignacion.resguardante'])->findOrFail($id);

        // Utilizando el prefijo para la vista
        return view($prefix . '.bajas.detalle', compact('baja')); // Aquí se agrega el prefijo a la vista
    }


    /**
     * Descargar el expediente de una baja directamente desde la base de datos.
     */


    public function buscar(Request $request)
    {
        $query = Baja::query();

        if ($request->has('buscar')) {
            $buscar = $request->input('buscar');
            $query->where('numero_inventario', 'like', "%$buscar%")
                ->orWhere('nombre', 'like', "%$buscar%");
        }

        if ($request->has('fecha_baja') && $request->input('fecha_baja') != '') {
            $fechaFiltro = match ($request->input('fecha_baja')) {
                'hoy' => now()->toDateString(),
                'semana' => now()->subWeek()->toDateString(),
                'mes' => now()->subMonth()->toDateString(),
                default => null
            };

            if ($fechaFiltro) {
                $query->where('fecha_baja', '>=', $fechaFiltro);
            }
        }

        $bajas = $query->orderBy('fecha_baja', 'desc')->limit(6)->get();

        return response()->json([
            'resultados' => $bajas
        ]);
    }


    public function getAllBajas()
    {
        $bajas = Baja::select('id', 'numero_inventario', 'nombre', 'fecha_baja')->get();
        return response()->json($bajas);
    }

    public function listarBienesInactivos(Request $request)
    {
        $query = Bien::where('estado', 'inactivo');

        // Obtener el término de búsqueda
        $search = $request->input('search', '');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_inventario', 'like', "%$search%")
                    ->orWhere('nombre', 'like', "%$search%");
            });
        }

        // Obtener el total de registros sin filtros
        $totalRecords = Bien::where('estado', 'inactivo')->count();

        // Aplicar paginación manual con DataTables (skip y take)
        $bienes = $query->orderBy('numero_inventario', 'asc')
            ->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => !empty($search) ? $query->count() : $totalRecords,
            'data' => $bienes,
        ]);
    }




}
