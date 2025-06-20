<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BienesImport;

class ImportBienesController extends Controller
{
    public function importar(Request $request)
    {
        // Validar que el archivo sea un Excel
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,csv|max:2048',
        ]);

        try {
            // Importar el archivo
            Excel::import(new BienesImport, $request->file('archivo'));

            return back()->with('success', 'Los bienes han sido importados correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

}
