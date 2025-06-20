<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exports\BienesExport;

class ExportarTodosBienesController extends Controller
{
    public function exportar()
    {
        // Llamamos a la clase de exportación
        $bienesExport = new BienesExport();
        $spreadsheet = $bienesExport->generarExcel();

        // Descargar el archivo
        $writer = new Xlsx($spreadsheet);
        $fileName = 'todos_bienes_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
