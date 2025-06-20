<?php

namespace App\Http\Controllers;

use App\Models\InventarioFisico;
use Illuminate\Http\Request;

class InventarioFisicoController extends Controller
{
    /**
     * Muestra una lista paginada de los inventarios físicos.
     */
    public function index()
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';
        $inventarios = InventarioFisico::paginate(5);
        return view($prefix . '.inventario_fisico.index', compact('inventarios'));
    }


    /**
     * Muestra el formulario para crear un nuevo inventario físico.
     */
    public function create()
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';
        return view($prefix . '.inventario_fisico.create');
    }


    /**
     * Almacena un nuevo inventario físico en la base de datos.
     */
    public function store(Request $request)
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';
        $request->validate([
            'nombre_inventario' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_finalizacion' => 'required|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:Programado,Completado',
            'comentario' => 'nullable|string',
        ]);

        InventarioFisico::create($request->all());

        return redirect()->route($prefix . '.inventario_fisico.index')->with('success', 'Inventario físico creado correctamente.');
    }


    /**
     * Muestra los detalles de un inventario físico específico.
     */
    public function show($id)
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';
        $inventario = InventarioFisico::findOrFail($id);
        return view($prefix . '.inventario_fisico.show', compact('inventario'));
    }


    /**
     * Muestra el formulario para editar un inventario físico existente.
     */
    public function edit($id)
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';
        $inventario = InventarioFisico::findOrFail($id);
        return view($prefix . '.inventario_fisico.edit', compact('inventario'));
    }


    /**
     * Actualiza un inventario físico específico en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';
        $request->validate([
            'nombre_inventario' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_finalizacion' => 'required|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:Programado,Completado',
            'comentario' => 'nullable|string',
        ]);

        $inventario = InventarioFisico::findOrFail($id);
        $inventario->update($request->all());

        return redirect()->route($prefix . '.inventario_fisico.index')->with('success', 'Inventario físico actualizado correctamente.');
    }


    /**
     * Elimina un inventario físico específico de la base de datos.
     */
    public function destroy($id)
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';
        $inventario = InventarioFisico::findOrFail($id);
        $inventario->delete();

        return redirect()->route($prefix . '.inventario_fisico.index')->with('success', 'Inventario físico eliminado correctamente.');
    }

}
