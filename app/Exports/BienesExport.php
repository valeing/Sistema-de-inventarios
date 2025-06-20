<?php

namespace App\Exports;

use App\Models\Bien;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BienesExport
{
    public function generarExcel()
    {
        // Obtener todos los bienes
        $bienes = Bien::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezado principal
        $sheet->setCellValue('A1', 'LISTA COMPLETA DE BIENES');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFCFE2F3');

        // Encabezado de la tabla de bienes
        $encabezados = [
            'A3' => 'No. DE INVENTARIO',
            'B3' => 'No. DE SERIE',
            'C3' => 'NOMBRE',
            'D3' => 'ESTADO DEL BIEN',
            'E3' => 'DESCRIPCIÓN GENERAL',
            'F3' => 'OBSERVACIONES',
            'G3' => 'FECHA DE ADQUISICIÓN',
            'H3' => 'DEPARTAMENTO',
            'I3' => 'CATEGORÍA',
            'J3' => 'COSTO (MXN)',
            'K3' => 'RESGUARDANTE'
        ];

        foreach ($encabezados as $columna => $titulo) {
            $sheet->setCellValue($columna, $titulo);
        }

        $sheet->getStyle('A3:K3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3:K3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:K3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9EAD3');
        $sheet->getStyle('A3:K3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);

        // Contenido de los bienes
        $row = 4;
        foreach ($bienes as $bien) {
            $sheet->setCellValueExplicit("A{$row}", $bien->numero_inventario, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("B{$row}", $bien->numero_serie ?? 'N/A', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", $bien->nombre ?? 'N/A');
            $sheet->setCellValue("D{$row}", ucfirst($bien->estado));
            $sheet->setCellValue("E{$row}", $bien->descripcion_general ?? 'N/A');
            $sheet->setCellValue("F{$row}", $bien->observaciones ?? 'N/A');
            $sheet->setCellValue("G{$row}", $bien->fecha_adquisicion ?? 'N/A');
            $sheet->setCellValue("H{$row}", $bien->departamento->nombre ?? 'N/A');
            $sheet->setCellValue("I{$row}", $bien->categoria ?? 'N/A');

            // **Formato del costo** con separación de miles y decimales
            $costoFormato = number_format($bien->costo, 2, '.', ',') . ' MXN';
            $sheet->setCellValue("J{$row}", $costoFormato);

            // Obtener resguardante correctamente
            if ($bien->asignacion && $bien->asignacion->resguardante) {
                $sheet->setCellValue("K{$row}", $bien->asignacion->resguardante->nombre_apellido);
            } else {
                $sheet->setCellValue("K{$row}", 'No asignado');
            }

            $row++;
        }

        // Ajuste de tamaño de columnas
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
