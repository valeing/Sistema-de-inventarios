<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Departamento;
use App\Models\Baja;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\QueryException;
use App\Models\Direccion;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use App\Models\Resguardante;


class BienController extends Controller
{

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }
    public function index(Request $request)
    {
        // Definir el prefijo según el rol del usuario autenticado
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $categoria = $request->input('categoria');
        $search = $request->input('search');

        $query = Bien::with('resguardante');

        if (!empty($categoria)) {
            $query->where('categoria', $categoria);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%$search%")
                    ->orWhere('numero_inventario', 'like', "%$search%")
                    ->orWhere('numero_serie', 'like', "%$search%")
                    ->orWhereHas('resguardante', function ($resguardanteQuery) use ($search) {
                        $resguardanteQuery->where('nombre_apellido', 'like', "%$search%");
                    });
            });
        }

        $bienes = $query->orderBy('fecha_adquisicion', 'desc')->paginate(12)->appends([
            'categoria' => $categoria,
            'search' => $search
        ]);

        // Si es una solicitud AJAX, devolver datos en JSON
        if ($request->ajax()) {
            return response()->json(
                $bienes->map(function ($bien) use ($prefix) {
                    return [
                        'id' => $bien->id_bien,
                        'nombre' => $bien->nombre,
                        'numero_inventario' => $bien->numero_inventario,
                        'numero_serie' => $bien->numero_serie ?? 'No disponible',
                        'costo' => $bien->costo ?? 'No disponible',
                        'resguardante' => $bien->resguardante ? $bien->resguardante->nombre_apellido : 'No asignado',
                        'url' => route("$prefix.bienes.show", $bien->id_bien),
                    ];
                })
            );
        }

        return view("{$prefix}.bienes.index", compact('bienes', 'categoria', 'search'));
    }







    public function create()
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';
        $departamentos = Departamento::all();
        return view("$prefix.bienes.register", compact('departamentos'));
    }




    public function store(Request $request)
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $validatedData = $request->validate([
            'numero_inventario' => 'required|unique:bienes|max:255',
            'numero_serie' => 'nullable|max:255',
            'nombre' => 'required|max:255',
            'descripcion_general' => 'nullable',
            'observaciones' => 'nullable',
            'fecha_adquisicion' => 'required|date',
            'id_departamento' => 'required|exists:departamentos,id_departamento',
            'categoria' => 'required',
            'estado' => 'required',
            'costo' => 'required|numeric|min:0|max:9999999.99',
            'imagen' => 'nullable|image|mimes:jpeg,jpg,png|max:51200',
        ]);

        try {
            // Convertir costo a número sin comas y con 2 decimales
            $validatedData['costo'] = number_format((float) str_replace(',', '', $validatedData['costo']), 2, '.', '');

            // Procesamiento de imagen (si se sube una)
            if ($request->hasFile('imagen')) {
                $file = $request->file('imagen');
                $imagePath = $file->getRealPath();
                $image = imagecreatefromstring(file_get_contents($imagePath));
                list($width, $height) = getimagesize($imagePath);

                // Redimensionar si es necesario
                $maxWidth = 1500;
                $maxHeight = 1500;
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = $width * $ratio;
                $newHeight = $height * $ratio;

                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                ob_start();
                imagejpeg($resizedImage, null, 75);
                $imageData = ob_get_contents();
                ob_end_clean();

                $validatedData['imagen'] = base64_encode($imageData);
                $validatedData['mime_type'] = 'image/jpeg';

                imagedestroy($image);
                imagedestroy($resizedImage);
            }

            // Guardar el bien
            $bien = Bien::create($validatedData);

            // **Generar Código QR** (SIEMPRE usando la ruta pública)
            $codigoQR = QrCode::format('svg')->size(200)->generate(route('bienes.public_show', $bien->id_bien));
            $bien->codigo_qr = base64_encode($codigoQR);
            $bien->save();

            return redirect()->route("$prefix.bienes.index")->with('success', 'Bien registrado correctamente.');
        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Ocurrió un error al registrar el bien.'])->withInput();
        }
    }




    public function generatePDF($id_bien)
    {
        // 🔹 Obtener el bien con sus relaciones
        $bien = Bien::with(['asignacion.resguardante', 'departamento'])->findOrFail($id_bien);

        // 🔹 Generar el Código QR con la URL pública del bien
        $qrCodeSvg = QrCode::format('svg')->size(200)->generate(route('bienes.public_show', $bien->id_bien));
        $qrCodeSvgBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        // 🔹 Convertir el logo en Base64 para garantizar que se renderice en el PDF
        $logoPath = public_path('img/Logo1.jpeg');
        $logoBase64 = file_exists($logoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        // 🔹 Crear la instancia de mPDF sin la barra invertida
        $mpdf = new Mpdf();

        // 🔹 Renderizar la vista Blade y pasar variables
        $html = View::make('pdf.bien_detalle', compact('bien', 'qrCodeSvgBase64', 'logoBase64'))->render();

        // 🔹 Escribir el HTML en el PDF
        $mpdf->WriteHTML($html);

        // 🔹 Generar y retornar el PDF con el nombre basado en el ID del bien
        return response($mpdf->Output('Detalles_Bien_' . $bien->id_bien . '.pdf', 'D'))
            ->header('Content-Type', 'application/pdf');
    }








    public function show($id_bien)
    {

        $bien = Bien::with('departamento.direccion')->findOrFail($id_bien);
        return view(app('prefix') . '.bienes.show', compact('bien'));
    }


    public function edit($id_bien)
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $bien = Bien::with('departamento')->findOrFail($id_bien); // 🔹 Asegurar que se carga la relación departamento
        $departamentos = Departamento::with('direccion')->get();

        return view("$prefix.bienes.edit", compact('bien', 'departamentos'));
    }


    public function update(Request $request, $id_bien)
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $validatedData = $request->validate([
            'numero_inventario' => 'required|max:255|unique:bienes,numero_inventario,' . $id_bien . ',id_bien',
            'numero_serie' => 'nullable|max:255',
            'nombre' => 'required|max:255',
            'descripcion_general' => 'nullable',
            'observaciones' => 'nullable',
            'fecha_adquisicion' => 'required|date',
            'id_departamento' => 'required|exists:departamentos,id_departamento',
            'categoria' => 'required',
            'estado' => 'required',
            'imagen' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:51200',
        ]);

        $bien = Bien::findOrFail($id_bien);

        try {
            if ($request->hasFile('imagen')) {
                $file = $request->file('imagen');
                $imagePath = $file->getRealPath();
                $imageType = exif_imagetype($imagePath);

                switch ($imageType) {
                    case IMAGETYPE_JPEG:
                        $image = imagecreatefromjpeg($imagePath);
                        break;
                    case IMAGETYPE_PNG:
                        $image = imagecreatefrompng($imagePath);
                        break;
                    case IMAGETYPE_GIF:
                        $image = imagecreatefromgif($imagePath);
                        break;
                    default:
                        return back()->withErrors(['error' => 'Formato de imagen no compatible.'])->withInput();
                }

                list($width, $height) = getimagesize($imagePath);
                $maxWidth = 1500;
                $maxHeight = 1500;

                if ($width > $maxWidth || $height > $maxHeight) {
                    $ratio = min($maxWidth / $width, $maxHeight / $height);
                    $newWidth = round($width * $ratio);
                    $newHeight = round($height * $ratio);
                } else {
                    $newWidth = $width;
                    $newHeight = $height;
                }

                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                ob_start();
                imagejpeg($resizedImage, null, 75);
                $imageData = ob_get_contents();
                ob_end_clean();

                $validatedData['imagen'] = base64_encode($imageData);
                $validatedData['mime_type'] = 'image/jpeg';

                imagedestroy($image);
                imagedestroy($resizedImage);
            }

            $bien->update($validatedData);

            // Generar QR con el prefijo adecuado
            $codigoQR = QrCode::format('svg')->size(200)->generate(route('bienes.public_show', $bien->id_bien));
            $bien->codigo_qr = base64_encode($codigoQR);
            $bien->save();

            return redirect()->route("$prefix.bienes.index")->with('success', 'Bien actualizado correctamente.');
        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Ocurrió un error al actualizar el bien.'])->withInput();
        }
    }





    public function destroy($id_bien)
    {
        $prefix = auth()->user()->role->name === 'Administrador' ? 'admin' : 'operador';

        $bien = Bien::with('asignacion.resguardante')->findOrFail($id_bien);

        if ($bien->estado === 'activo') {
            return redirect()->route("$prefix.bienes.index")->with('error', 'El bien está activo y no se puede eliminar.');
        }

        if ($bien->asignacion && $bien->asignacion->resguardante) {
            return redirect()->route("$prefix.bienes.index")->with('error', 'El bien está asignado a un resguardante y no se puede eliminar.');
        }

        $baja = Baja::where('id_bien', $id_bien)->first();
        if (!$baja || !$baja->expediente) {
            return redirect()->route("$prefix.bienes.index")->with('error', 'Es necesario subir el expediente de baja para eliminar el bien.');
        }

        $bien->delete();

        return redirect()->route("$prefix.bienes.index")->with('success', 'El bien ha sido eliminado correctamente.');
    }




    public function verPublico($id)
    {
        $bien = Bien::with(['asignacion.resguardante', 'departamento'])->findOrFail($id);
        return view('bienes.public_show', compact('bien'));
    }


    public function descargarEtiquetasPDF(Request $request)
    {
        ini_set('max_execution_time', 300);

        // Verificar si hay bienes seleccionados
        if (!$request->has('bienes') || empty($request->bienes)) {
            return back()->with('error', 'Selecciona al menos un bien para descargar el código QR.');
        }

        // Convertir JSON a array
        $bienesIds = json_decode($request->bienes, true);

        // Obtener bienes seleccionados con sus relaciones
        $bienes = Bien::whereIn('id_bien', $bienesIds)->with('departamento')->get();

        if ($bienes->isEmpty()) {
            return back()->with('error', 'No se encontraron bienes seleccionados.');
        }

        // Configurar PDF
        $mpdf = new Mpdf(['format' => 'A4', 'mode' => 'utf-8', 'orientation' => 'P']);

        // Obtener logo
        $logoPath = public_path('img/Logo1.jpeg');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        // Generar contenido HTML para PDF
        $html = View::make('pdf.etiqueta_pdf', compact('bienes', 'logoBase64'))->render();
        $mpdf->WriteHTML($html);

        return Response::make($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Etiquetas_Bienes.pdf"');
    }






}
