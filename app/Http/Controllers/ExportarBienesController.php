<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Bien;
use App\Models\Resguardante;

class ExportarBienesController extends Controller
{
    public function exportar(Request $request)
{
    $bienesIds = $request->input('bienes');

    if (!$bienesIds || !is_array($bienesIds)) {
        return redirect()->back()->with('error', 'No se seleccionaron bienes para exportar.');
    }

    $bienes = Bien::whereIn('id_bien', $bienesIds)
        ->with(['asignacion.resguardante.direccion', 'asignacion.resguardante.departamento'])
        ->get();

    $resguardante = $bienes->first()?->asignacion?->resguardante;

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Encabezado principal
    $sheet->setCellValue('A1', 'RESGUARDO DE BIENES PATRIMONIALES ASIGNADOS');
    $sheet->mergeCells('A1:E1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFCFE2F3');

    $sheet->setCellValue('A2', 'DEPARTAMENTO DE RECURSOS MATERIALES');
    $sheet->mergeCells('A2:E2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A2')->getFont()->setBold(true);

    // Información del resguardante
    $sheet->setCellValue('A4', 'NOMBRE DEL RESGUARDANTE:');
    $sheet->mergeCells('A4:B4');
    $sheet->setCellValue('C4', $resguardante->nombre_apellido ?? 'N/A');
    $sheet->mergeCells('C4:D4');

    $sheet->setCellValue('A5', 'DIRECCIÓN:');
    $sheet->mergeCells('A5:B5');
    $sheet->setCellValue('C5', $resguardante->direccion->nombre ?? 'N/A');
    $sheet->mergeCells('C5:D5');

    $sheet->setCellValue('A6', 'DEPARTAMENTO:');
    $sheet->mergeCells('A6:B6');
    $sheet->setCellValue('C6', $resguardante->departamento->nombre ?? 'N/A');
    $sheet->mergeCells('C6:D6');

    $sheet->setCellValue('E4', 'FECHA:');
    $sheet->setCellValue('F4', now()->format('d/m/Y'));

    // Encabezado de la tabla de bienes
    $sheet->setCellValue('A8', 'No. DE INVENTARIO');
    $sheet->setCellValue('B8', 'No. DE SERIE');
    $sheet->setCellValue('C8', 'NOMBRE');
    $sheet->setCellValue('D8', 'ESTADO DEL BIEN');
    $sheet->setCellValue('E8', 'OBSERVACIONES');

    $sheet->getStyle('A8:E8')->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A8:E8')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A8:E8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFD9EAD3');
    $sheet->getStyle('A8:E8')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

    // Contenido de los bienes
    $row = 9;
    foreach ($bienes as $bien) {
        $sheet->setCellValue("A{$row}", $bien->numero_inventario);
        $sheet->setCellValue("B{$row}", $bien->numero_serie ?? 'N/A');
        $sheet->setCellValue("C{$row}", $bien->nombre ?? 'N/A');
        $sheet->setCellValue("D{$row}", $bien->estado === 'activo' ? 'Activo' : 'Inactivo'); // Cambiado para reflejar los estados de la base de datos
        $sheet->setCellValue("E{$row}", $bien->observaciones ?? 'N/A');
        $row++;
    }

    // Espacio para las firmas
    $row += 2; // Espaciado

    // Línea para "ENTREGA BIEN"
    $sheet->setCellValue("A{$row}", 'ENTREGA BIEN:');
    $sheet->mergeCells("A{$row}:C{$row}");
    $sheet->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);

    $sheet->setCellValue("A" . ($row + 1), '--------------------------------');
    $sheet->mergeCells("A" . ($row + 1) . ":C" . ($row + 1));

    $sheet->setCellValue("A" . ($row + 2), 'LIC. FELIX ABRAHAM ROMERO LOPEZ');
    $sheet->mergeCells("A" . ($row + 2) . ":C" . ($row + 2));
    $sheet->getStyle("A" . ($row + 2))->getAlignment()->setHorizontal('center');

    $sheet->setCellValue("A" . ($row + 3), 'JEFE DEL DEPTO. DE RECURSOS MATERIALES');
    $sheet->mergeCells("A" . ($row + 3) . ":C" . ($row + 3));
    $sheet->getStyle("A" . ($row + 3))->getAlignment()->setHorizontal('center');

    // Espacio en blanco para el sello
    $sheet->setCellValue("A" . ($row + 4), 'SELLO');
    $sheet->mergeCells("A" . ($row + 4) . ":C" . ($row + 5));
    $sheet->getStyle("A" . ($row + 4))->getAlignment()->setHorizontal('center');

    // Línea para "RECIBE BIEN"
    $sheet->setCellValue("D{$row}", 'RECIBE BIEN:');
    $sheet->mergeCells("D{$row}:E{$row}");
    $sheet->getStyle("D{$row}:E{$row}")->getFont()->setBold(true);

    $sheet->setCellValue("D" . ($row + 1), '--------------------------------');
    $sheet->mergeCells("D" . ($row + 1) . ":E" . ($row + 1));

    $sheet->setCellValue("D" . ($row + 2), $resguardante->nombre_apellido ?? 'N/A');
    $sheet->mergeCells("D" . ($row + 2) . ":E" . ($row + 2));
    $sheet->getStyle("D" . ($row + 2))->getAlignment()->setHorizontal('center');

    $sheet->setCellValue("D" . ($row + 3), $resguardante->departamento->nombre ?? 'N/A');
    $sheet->mergeCells("D" . ($row + 3) . ":E" . ($row + 3));
    $sheet->getStyle("D" . ($row + 3))->getAlignment()->setHorizontal('center');

    // Descargar el archivo
    $writer = new Xlsx($spreadsheet);
    $fileName = 'resguardo_bienes_' . now()->format('Ymd_His') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}

}
