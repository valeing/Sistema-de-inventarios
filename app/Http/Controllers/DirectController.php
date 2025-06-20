<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use App\Models\Departamento;
use App\Models\Bien;
use App\Models\Resguardante;
use Illuminate\Http\Request;

class DirectController extends Controller
{
    /**
     * Mostrar todas las direcciones con sus departamentos.
     */
    public function index()
    {
        // Obtener el prefijo dinámico según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        // Obtener las direcciones con sus departamentos y paginarlas
        $direcciones = Direccion::with('departamentos')->paginate(10);

        // Pasar el prefijo a la vista
        return view("$prefix.direct.ver_direcciones", compact('direcciones', 'prefix'));
    }


    /**
     * Mostrar formulario de creación de dirección y departamentos.
     */
    public function create()
    {
        // Obtener el prefijo dinámico según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        // Retornar la vista con el prefijo
        return view("$prefix.direct.create");
    }


    /**
     * Guardar una nueva dirección con sus departamentos asociados.
     */
    public function store(Request $request)
    {
        // Obtener el prefijo dinámico según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        // Validación de los datos del formulario
        $request->validate([
            'nombre_direccion' => 'required|string|max:255',
            'nombre_departamentos.*' => 'nullable|string|max:255',
        ]);

        // Formatear el nombre de la dirección
        $nombreDireccion = ucfirst(strtolower($request->nombre_direccion));

        // Crear la dirección
        $direccion = Direccion::create([
            'nombre' => $nombreDireccion,
        ]);

        // Crear departamentos si se proporcionan
        if ($request->has('nombre_departamentos')) {
            foreach ($request->nombre_departamentos as $nombreDepartamento) {
                if (!empty($nombreDepartamento)) {
                    $direccion->departamentos()->create([
                        'nombre' => ucfirst(strtolower($nombreDepartamento)),
                    ]);
                }
            }
        }

        // Redirigir con mensaje de éxito
        return redirect()->route("$prefix.direct.index")->with('success', 'Dirección y departamentos agregados correctamente.');
    }


    /**
     * Mostrar formulario de edición de una dirección y sus departamentos.
     */
    public function edit($id)
    {
        // Obtener el prefijo dinámico según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        // Buscar la dirección con sus departamentos
        $direccion = Direccion::with('departamentos')->findOrFail($id);

        // Retornar la vista de edición con el prefijo dinámico
        return view("$prefix.direct.update", compact('direccion'));
    }


    /**
     * Actualizar una dirección y sus departamentos.
     */
    public function update(Request $request, $id_direccion)
    {
        // Obtener el prefijo dinámico según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        // Validación de los datos del formulario
        $request->validate([
            'nombre_direccion' => 'required|string|max:255',
            'departamentos_existentes.*' => 'nullable|string|max:255',
            'departamentos_nuevos.*' => 'nullable|string|max:255',
        ]);

        // Buscar la dirección por ID
        $direccion = Direccion::findOrFail($id_direccion);
        $direccion->update(['nombre' => ucfirst(strtolower($request->nombre_direccion))]);

        // ACTUALIZAR DEPARTAMENTOS EXISTENTES
        if ($request->has('departamentos_existentes')) {
            foreach ($request->departamentos_existentes as $id_departamento => $nombreDepartamento) {
                if (!empty($nombreDepartamento)) {
                    $departamento = Departamento::find($id_departamento);
                    if ($departamento) {
                        $departamento->update(['nombre' => ucfirst(strtolower($nombreDepartamento))]);
                    }
                }
            }
        }

        // AGREGAR NUEVOS DEPARTAMENTOS
        if ($request->has('departamentos_nuevos')) {
            foreach ($request->departamentos_nuevos as $nombreDepartamento) {
                if (!empty($nombreDepartamento)) {
                    $direccion->departamentos()->create([
                        'nombre' => ucfirst(strtolower($nombreDepartamento)),
                    ]);
                }
            }
        }

        // Redirigir al índice de direcciones con un mensaje de éxito
        return redirect()->route("$prefix.direct.index")->with('success', 'Dirección y departamentos actualizados correctamente.');
    }


    /**
     * Eliminar un departamento individualmente.
     */
    public function destroyDepartamento($id_departamento)
    {
        $departamento = Departamento::findOrFail($id_departamento);

        // Verificar si hay bienes o resguardantes asociados antes de eliminar
        $bienesAsociados = Bien::where('id_departamento', $departamento->id_departamento)->exists();
        $resguardantesAsociados = Resguardante::where('id_departamento', $departamento->id_departamento)->exists();

        if ($bienesAsociados || $resguardantesAsociados) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el departamento porque tiene bienes o resguardantes asociados.'
            ], 400);
        }

        $departamento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Departamento eliminado correctamente.'
        ]);
    }

    /**
     * Eliminar una dirección con sus departamentos.
     */
    public function destroy($id_direccion)
    {
        // Obtener el prefijo dinámico según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $direccion = Direccion::with('departamentos')->findOrFail($id_direccion);

        // Verificar si hay bienes o resguardantes asociados
        $departamentosIds = $direccion->departamentos->pluck('id_departamento');
        $bienesAsociados = Bien::whereIn('id_departamento', $departamentosIds)->exists();
        $resguardantesAsociados = Resguardante::whereIn('id_departamento', $departamentosIds)->exists();

        if ($bienesAsociados || $resguardantesAsociados) {
            return redirect()->route("$prefix.direct.index")->with('error', 'No se puede eliminar la dirección porque hay bienes o resguardantes asignados a sus departamentos.');
        }

        $direccion->delete();

        return redirect()->route("$prefix.direct.index")->with('success', 'Dirección y sus departamentos eliminados correctamente.');
    }

}
