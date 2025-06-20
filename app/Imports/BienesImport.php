<?php
namespace App\Imports;

use App\Models\Bien;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BienesImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        if (
            !isset($row['numero_inventario']) || empty(trim($row['numero_inventario'])) ||
            Bien::where('numero_inventario', trim($row['numero_inventario']))->exists()
        ) {
            return null;
        }

        $fechaAdquisicion = Carbon::now()->format('Y-m-d');

        if (!empty($row['fecha_adquisicion'])) {
            if (is_numeric($row['fecha_adquisicion'])) {
                $fechaAdquisicion = Carbon::createFromDate(1900, 1, 1)->addDays($row['fecha_adquisicion'] - 2)->format('Y-m-d');
            } else {
                try {
                    $fechaAdquisicion = Carbon::parse($row['fecha_adquisicion'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaAdquisicion = Carbon::now()->format('Y-m-d');
                }
            }
        }

        // ✅ Leer imagen predeterminada y convertirla a base64
        $defaultImagePath = public_path('img/No imagen.jpeg');
        $base64Image = null;
        $mimeType = null;

        if (file_exists($defaultImagePath)) {
            $imageData = file_get_contents($defaultImagePath);
            $base64Image = base64_encode($imageData);
            $mimeType = mime_content_type($defaultImagePath); // Por ejemplo, 'image/png'
        }

        // Crear el nuevo bien
        $bien = Bien::create([
            'numero_inventario' => trim($row['numero_inventario']),
            'numero_serie' => $row['numero_serie'] ?? 'N/A',
            'nombre' => $row['nombre'] ?? 'SIN NOMBRE',
            'descripcion_general' => $row['descripcion_general'] ?? 'SIN DESCRIPCIÓN',
            'observaciones' => $row['observaciones'] ?? null,
            'fecha_adquisicion' => $fechaAdquisicion,
            'categoria' => $row['categoria'] ?? 'SIN CATEGORÍA',
            'estado' => isset($row['estado']) && in_array(strtolower($row['estado']), ['activo', 'inactivo'])
                ? strtolower($row['estado'])
                : 'activo',
            'costo' => isset($row['costo']) && is_numeric($row['costo']) ? floatval($row['costo']) : 0.0,
            'imagen' => $base64Image, // ✅ Guardar imagen base64
            'mime_type' => $mimeType, // ✅ Guardar tipo mime
        ]);

        // Código QR como siempre
        $codigoQR = QrCode::format('svg')->size(200)->generate(route('bienes.public_show', $bien->id_bien));
        $bien->codigo_qr = base64_encode($codigoQR);
        $bien->save();

        return $bien;
    }

}
