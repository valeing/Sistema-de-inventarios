<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialReporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporte_id',
        'resguardante_id',
        'bien_id',
        'comentario',
        'comentario_rechazo', // ✅ campo nuevo
        'fecha_reporte',
        'estatus',
        'fecha_eliminacion',
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class, 'bien_id');
    }

    public function resguardante()
    {
        return $this->belongsTo(Resguardante::class, 'resguardante_id');
    }
}
