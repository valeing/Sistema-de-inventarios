<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Baja extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_bien',
        'motivo',
        'descripcion_problema',
        'fecha_baja',
        'expediente', // 🔹 Guardamos el PDF en la base de datos
        'mime_type_expediente', // 🔹 Se almacena el tipo MIME del expediente
        'nombre_apellido',
        'categoria',
        'estado',
        'departamento',
        'numero_inventario',
        'numero_serie',
        'nombre',
        'descripcion_general',
        'observaciones',
        'fecha_adquisicion',
        'mime_type',
        'imagen',
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class, 'id_bien', 'id_bien');
    }

    public function resguardante()
    {
        return $this->belongsTo(Resguardante::class, 'id_resguardante');
    }
}
