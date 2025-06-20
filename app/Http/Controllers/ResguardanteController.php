<?php

namespace App\Http\Controllers;

use App\Models\Resguardante;
use App\Models\Direccion;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class ResguardanteController extends Controller
{
    /**
     * Mostrar todos los resguardantes con filtros de búsqueda y dirección.
     */
    public function index(Request $request)
    {
        // Obtener el prefijo dinámico
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $direcciones = Direccion::with('departamentos')->get();
        $query = Resguardante::query()->with(['direccion', 'departamento']);

        // 📌 Manejo de AJAX (solo devuelve JSON si la solicitud es AJAX)
        if ($request->ajax()) {
            // Si hay búsqueda y/o filtro por dirección
            if ($request->filled('search') || $request->filled('direccion')) {
                if ($request->filled('direccion')) {
                    $query->where('id_direccion', $request->direccion);
                }

                if ($request->filled('search')) {
                    $query->where(function ($q) use ($request) {
                        $q->where('nombre_apellido', 'like', '%' . $request->search . '%')
                            ->orWhere('numero_empleado', 'like', '%' . $request->search . '%');
                    });
                }

                return response()->json($query->get([
                    'id_resguardante',
                    'nombre_apellido',
                    'numero_empleado',
                    'id_direccion'
                ]));
            }

            // Si solo se selecciona una dirección, devolver los departamentos relacionados
            if ($request->filled('direccion')) {
                $departamentos = Departamento::where('id_direccion', $request->direccion)->get();
                return response()->json($departamentos);
            }
        }

        // 📌 Manejo de Recarga de Página (Método GET tradicional)
        if ($request->filled('direccion')) {
            $query->where('id_direccion', $request->direccion);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre_apellido', 'like', '%' . $request->search . '%')
                    ->orWhere('numero_empleado', 'like', '%' . $request->search . '%');
            });
        }

        $resguardantes = $query->paginate(10); // Paginación de 5 registros por página

        return view("$prefix.resguardantes.index", compact('resguardantes', 'direcciones'));
    }




    /**
     * Mostrar los detalles de un resguardante específico.
     */
    public function show($id_resguardante, Request $request)
    {
        // Obtener el prefijo dinámico según el rol del usuario
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $resguardante = Resguardante::with([
            'direccion',
            'departamento',
            'asignaciones.bien' => function ($query) {
                $query->select('id_bien', 'nombre', 'categoria', 'numero_inventario', 'descripcion_general', 'estado', 'observaciones');
            }
        ])->findOrFail($id_resguardante);

        // Verificar si la solicitud proviene del buscador o de la tabla
        if ($request->ajax()) {
            return response()->json($resguardante);
        }

        // Paginación solo para la tabla de bienes asignados cuando no es AJAX
        $bienes_asignados = $resguardante->asignaciones()->with('bien')->paginate(12);

        return view("$prefix.resguardantes.show", compact('resguardante', 'bienes_asignados'));
    }



    /**
     * Crear un nuevo resguardante.
     */
    public function create()
    {
        // Obtener el prefijo dinámico según el rol del usuario
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $direcciones = Direccion::all(); // Obtener todas las direcciones disponibles
        return view("$prefix.resguardantes.register", compact('direcciones'));
    }


    public function store(Request $request)
    {
        // Obtener el prefijo dinámico según el rol del usuario
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $validatedData = $request->validate([
            'numero_empleado' => 'required|unique:resguardantes|max:255',
            'nombre_apellido' => 'required|max:255',
            'fecha' => 'required|date',
            'id_direccion' => 'required|exists:direcciones,id_direccion',
            'id_departamento' => 'required|exists:departamentos,id_departamento',
            'estado' => 'required|in:activo,inactivo',
            'telefono' => 'nullable|max:15',
        ]);

        Resguardante::create($validatedData);

        return redirect()->route("$prefix.resguardantes.index")->with('success', 'Resguardante registrado correctamente.');
    }

    public function getDepartamentos($id_direccion)
    {
        // Obtener los departamentos relacionados con la dirección seleccionada
        $departamentos = Departamento::where('id_direccion', $id_direccion)->get();
        return response()->json($departamentos);
    }

    /**
     * Mostrar el formulario para editar un resguardante.
     */
    public function edit($id_resguardante)
    {
        // Obtener el prefijo dinámico según el rol del usuario
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $resguardante = Resguardante::findOrFail($id_resguardante);
        $direcciones = Direccion::all();
        $departamentos = Departamento::where('id_direccion', $resguardante->id_direccion)->get();

        return view("$prefix.resguardantes.edit", compact('resguardante', 'direcciones', 'departamentos'));
    }


    /**
     * Actualizar un resguardante.
     */
    public function update(Request $request, $id_resguardante)
    {
        // Obtener el prefijo dinámico según el rol del usuario
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $validatedData = $request->validate([
            'numero_empleado' => 'required|max:255|unique:resguardantes,numero_empleado,' . $id_resguardante . ',id_resguardante',
            'nombre_apellido' => 'required|max:255',
            'fecha' => 'required|date',
            'id_direccion' => 'required|exists:direcciones,id_direccion',
            'id_departamento' => 'required|exists:departamentos,id_departamento',
            'telefono' => 'nullable|digits:10',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $resguardante = Resguardante::findOrFail($id_resguardante);
        $resguardante->update($validatedData);

        return redirect()->route("$prefix.resguardantes.index")->with('success', 'Resguardante actualizado correctamente.');
    }


    /**
     * Eliminar un resguardante.
     */
    public function destroy($id_resguardante)
    {
        // Obtener el prefijo dinámico según el rol del usuario
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $resguardante = Resguardante::findOrFail($id_resguardante);

        if ($resguardante->asignaciones()->count() > 0) {
            return redirect()->route("$prefix.resguardantes.index")
                ->withErrors(['error' => 'El resguardante no se puede eliminar porque tiene bienes asignados.']);
        }

        $resguardante->delete();

        return redirect()->route("$prefix.resguardantes.index")
            ->with('success', 'Resguardante eliminado correctamente.');
    }

    /**
     * Obtener departamentos de una dirección específica.
     */
    public function obtenerDepartamentos($id_direccion)
    {
        $departamentos = Departamento::where('id_direccion', $id_direccion)->get();
        return response()->json($departamentos);
    }

    /**public function misBienes()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar si el usuario está vinculado a un resguardante
        if ($user->resguardante) {
            $resguardante = $user->resguardante;

            // Obtener los bienes asignados al resguardante a través de las asignaciones
            $bienes = $resguardante->bienes()->paginate(4);

            return view('Resguardante.mis_bienes', compact('bienes', 'resguardante'));
        }

        // Si el usuario no está vinculado a un resguardante, mostrar un mensaje
        return redirect()->route('home')->with('error', 'No tienes bienes asignados.');
    }

    */


    public function getAllResguardantes()
    {
        $resguardantes = Resguardante::select('id_resguardante', 'numero_empleado', 'nombre_apellido', 'id_direccion', 'id_departamento', 'fecha', 'estado')
            ->with(['direccion:id_direccion,nombre', 'departamento:id_departamento,nombre']) // Solo traer los datos necesarios
            ->get();

        return response()->json($resguardantes);
    }

}
