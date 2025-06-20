<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Resguardante;
use App\Models\Asignacion;
use Illuminate\Http\Request;
use App\Models\User;

class AsignacionController extends Controller
{
    /**
     * Mostrar el formulario para asignar bienes a un resguardante.
     */
    public function create()
    {
        // Determinar el prefijo dinámicamente según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $bienes_disponibles = Bien::whereNull('resguardante_id')
            ->orderBy('numero_inventario', 'asc') // Ordenar por N° Inventario
            ->paginate(10); // Mostrar 10 bienes por página

        $resguardantes = Resguardante::all();

        return view("$prefix.asignaciones.asignar_bienes", compact('bienes_disponibles', 'resguardantes'));
    }



    /**
     * Guardar la asignación de bienes a un resguardante.
     */
    public function store(Request $request)
    {
        // Determinar el prefijo dinámicamente según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $validatedData = $request->validate([
            'id_resguardante' => 'required|exists:resguardantes,id_resguardante',
            'id_bien' => 'required|array',
            'id_bien.*' => 'exists:bienes,id_bien',
            'fecha_asignacion' => 'required|date',
        ]);

        $resguardante = Resguardante::findOrFail($request->id_resguardante);

        foreach ($request->id_bien as $bienId) {
            $bien = Bien::findOrFail($bienId);

            if (is_null($bien->resguardante_id)) {
                $bien->update(['resguardante_id' => $resguardante->id_resguardante]);

                Asignacion::create([
                    'id_bien' => $bienId,
                    'id_resguardante' => $resguardante->id_resguardante,
                    'fecha_asignacion' => $request->fecha_asignacion,
                ]);
            }
        }

        return redirect()->route("$prefix.asignaciones.index")->with('success', 'Bien(es) asignado(s) correctamente.');
    }


    /**
     * Mostrar todas las asignaciones con bienes y resguardantes.
     */
    public function index(Request $request)
    {
        // Determinar el prefijo dinámicamente según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $query = Resguardante::whereHas('bienes')
            ->with([
                'bienes' => function ($query) {
                    $query->select('id_bien', 'numero_inventario', 'nombre', 'resguardante_id');
                }
            ])
            ->withCount('bienes') // Contar la cantidad de bienes asignados
            ->orderBy('nombre_apellido', 'asc'); // Ordenar por nombre

        // 🔹 Búsqueda AJAX por nombre de resguardante
        if ($request->ajax()) {
            if ($request->filled('search')) {
                $query->where('nombre_apellido', 'like', '%' . $request->search . '%');
            }

            $resultados = $query->get(['id_resguardante', 'nombre_apellido', 'bienes_count']); // Evita cargar relaciones innecesarias
            return response()->json($resultados);
        }

        $asignaciones = $query->paginate(10); // Paginación con Bootstrap 5

        return view("$prefix.asignaciones.index", compact('asignaciones'));
    }





    /**
     * Mostrar las asignaciones actuales de un resguardante.
     */
    public function show($id_resguardante)
    {
        // Determinar el prefijo dinámicamente según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $resguardante = Resguardante::findOrFail($id_resguardante);
        $bienes = $resguardante->bienes()->paginate(10);

        return view("$prefix.asignaciones.show", compact('resguardante', 'bienes'));
    }



    /**
     * Eliminar una asignación (desasignar un bien de un resguardante).
     */

    public function destroy($id_resguardante)
    {
        // Determinar el prefijo dinámicamente según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        // Obtener el resguardante
        $resguardante = Resguardante::findOrFail($id_resguardante);

        // Obtener todos los bienes asignados al resguardante
        $bienes = Bien::where('resguardante_id', $id_resguardante)->get();

        // Desasignar los bienes, quitando el resguardante
        foreach ($bienes as $bien) {
            $bien->update(['resguardante_id' => null]);
        }

        // Eliminar todas las asignaciones relacionadas
        Asignacion::where('resguardante_id', $id_resguardante)->delete();

        return redirect()->route("$prefix.asignaciones.index")->with('success', 'Todas las asignaciones han sido eliminadas correctamente.');
    }


    public function desasignarTodos($id_resguardante)
    {
        // Determinar el prefijo dinámicamente según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        // Obtener el resguardante
        $resguardante = Resguardante::findOrFail($id_resguardante);

        // Desvincular todos los bienes asignados a este resguardante
        Bien::where('resguardante_id', $id_resguardante)->update(['resguardante_id' => null]);

        // Eliminar todas las asignaciones registradas
        Asignacion::where('id_resguardante', $id_resguardante)->delete();

        return redirect()->route("$prefix.asignaciones.index")->with('success', 'Todos los bienes han sido desasignados correctamente.');
    }




    public function desasignarIndividual($id)
    {
        // Determinar el prefijo dinámicamente según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        // Buscar el bien y su asignación
        $bien = Bien::findOrFail($id);
        $asignacion = Asignacion::where('id_bien', $id)->first();

        if (!$asignacion) {
            return response()->json([
                'error' => 'El bien no está asignado.',
                'redirect' => route("$prefix.asignaciones.index")
            ], 400);
        }

        try {
            // Eliminar la asignación específica
            $asignacion->delete();

            // Desvincular el bien del resguardante en la tabla bienes
            $bien->update(['resguardante_id' => null]);

            return response()->json([
                'success' => 'Bien desasignado correctamente.',
                'redirect' => route("$prefix.asignaciones.index")
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Hubo un problema al desasignar el bien.',
                'redirect' => route("$prefix.asignaciones.index")
            ], 500);
        }
    }





    /**
     * Editar una asignación existente.
     */
    public function edit($id)
    {
        // Determinar el prefijo dinámicamente según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        // Buscar la asignación con el ID correcto
        $asignacion = Asignacion::where('id_bien', $id)->firstOrFail();

        // Obtener los bienes disponibles con paginación
        $bienes_disponibles = Bien::where(function ($query) use ($asignacion) {
            $query->whereNull('resguardante_id')
                ->orWhere('id_bien', $asignacion->id_bien);
        })->paginate(4); // 🔹 Se agregó paginación aquí

        // Obtener los resguardantes
        $resguardantes = Resguardante::all();

        return view("$prefix.asignaciones.edit", compact('asignacion', 'bienes_disponibles', 'resguardantes'));
    }





    /**
     * Actualizar la asignación editada.
     */
    public function update(Request $request, $id)
    {
        // Determinar el prefijo dinámicamente según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        // Buscar la asignación existente
        $asignacion = Asignacion::findOrFail($id);

        // Validar los datos del formulario
        $request->validate([
            'id_resguardante' => 'required|exists:resguardantes,id_resguardante',
            'id_bien' => 'required|exists:bienes,id_bien',
            'fecha_asignacion' => 'required|date',
        ]);

        // 🔹 1. Verificar si el bien cambió
        if ($asignacion->id_bien != $request->id_bien) {
            // Liberar el bien anterior (quitar su resguardante)
            Bien::where('id_bien', $asignacion->id_bien)->update(['resguardante_id' => null]);

            // Asignar el nuevo bien al nuevo resguardante
            Bien::where('id_bien', $request->id_bien)->update([
                'resguardante_id' => $request->id_resguardante
            ]);

            // 🔹 2. Actualizar la asignación con el nuevo bien
            $asignacion->update([
                'id_resguardante' => $request->id_resguardante,
                'id_bien' => $request->id_bien,
                'fecha_asignacion' => $request->fecha_asignacion,
            ]);
        } else {
            // 🔹 3. Solo actualizar el resguardante si el bien no cambió
            if ($asignacion->id_resguardante != $request->id_resguardante) {
                // Actualizar el bien con el nuevo resguardante
                Bien::where('id_bien', $request->id_bien)->update([
                    'resguardante_id' => $request->id_resguardante
                ]);
            }

            // Actualizar la asignación con los nuevos datos
            $asignacion->update([
                'id_resguardante' => $request->id_resguardante,
                'fecha_asignacion' => $request->fecha_asignacion,
            ]);
        }

        return redirect()->route("$prefix.asignaciones.index")->with('success', 'Asignación actualizada correctamente.');
    }




    /**
     * Buscar resguardantes por nombre o número de empleado.
     */
    public function buscarResguardantes(Request $request)
    {
        $query = $request->input('query');

        $resguardantes = Resguardante::where('nombre_apellido', 'LIKE', "%{$query}%")
            ->orWhere('numero_empleado', 'LIKE', "%{$query}%")
            ->get();

        return response()->json($resguardantes);
    }

    /**
     * Buscar bienes no asignados.
     */
    public function buscarBienes(Request $request)
    {
        $query = Bien::whereNull('resguardante_id');

        $search = $request->input('search', ''); // 🔹 Solo búsqueda por número de inventario y nombre

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_inventario', 'like', "%$search%")
                    ->orWhere('nombre', 'like', "%$search%");
            });
        }

        $totalRecords = Bien::whereNull('resguardante_id')->count();

        $bienes = $query->orderBy('numero_inventario', 'asc')
            ->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get();

        $resultados = $bienes->map(function ($bien) {
            return [
                'id_bien' => $bien->id_bien,
                'nombre' => $bien->nombre,
                'numero_inventario' => $bien->numero_inventario,
                'observaciones' => $bien->observaciones ?? '',
                'fecha_adquisicion' => $bien->fecha_adquisicion ?? '',
                'imagen_url' => $bien->imagen ? "data:image/jpeg;base64,{$bien->imagen}" : "https://via.placeholder.com/50",
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $search ? $query->count() : $totalRecords,
            'data' => $resultados,
        ]);
    }


    public function getBienes(Request $request)
    {
        $query = Bien::whereNull('resguardante_id');

        $search = $request->input('search', ''); // 🔹 Corregido para evitar errores

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_inventario', 'like', "%$search%")
                    ->orWhere('nombre', 'like', "%$search%");
            });
        }

        $totalRecords = Bien::whereNull('resguardante_id')->count();

        $bienes = $query->orderBy('numero_inventario', 'asc')
            ->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $search ? $query->count() : $totalRecords,
            'data' => $bienes,
        ]);
    }


}
