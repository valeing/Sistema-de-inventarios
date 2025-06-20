<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_asignacion';
    protected $table = 'asignaciones';

    protected $fillable = [
        'id_bien',
        'id_resguardante',
        'fecha_asignacion',
    ];

    /**
     * Relación con Bien.
     */
    public function bien()
    {
        return $this->belongsTo(Bien::class, 'id_bien', 'id_bien');
    }

    /**
     * Relación con Resguardante.
     */
    public function resguardante()
    {
        return $this->belongsTo(Resguardante::class, 'id_resguardante', 'id_resguardante');
    }
}
