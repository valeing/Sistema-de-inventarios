<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteBien extends Model
{
    use HasFactory;

    protected $table = 'reportes_bienes';

    protected $fillable = [
        'resguardante_id',
        'bien_id',
        'comentario',
        'fecha_reporte',
        'estatus',
        'comentario_rechazo', // ✅ nuevo campo agregado
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class, 'bien_id', 'id_bien');
    }

    public function resguardante()
    {
        return $this->belongsTo(Resguardante::class, 'resguardante_id', 'id_resguardante');
    }
}
