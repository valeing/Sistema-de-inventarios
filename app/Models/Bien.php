<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bien extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_bien';
    protected $table = 'bienes';

    protected $fillable = [
        'numero_inventario',
        'numero_serie',
        'nombre',
        'descripcion_general',
        'observaciones',
        'fecha_adquisicion',
        'categoria',
        'estado',
        'imagen',
        'mime_type',
        'codigo_qr',
        'id_departamento',
        'resguardante_id', // Agregar esta columna si no está
        'costo', // 🔹 Agregar el campo de costo
    ];


    /**
     * Relación con Departamento.
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    /**
     * Relación con Asignación.
     */
    public function asignacion()
    {
        return $this->hasOne(Asignacion::class, 'id_bien', 'id_bien');
    }

    /**
     * Relación directa con Resguardante.
     */
    public function resguardante()
    {
        return $this->belongsTo(Resguardante::class, 'resguardante_id', 'id_resguardante');
    }
    public function reportes()
    {
        return $this->hasMany(ReporteBien::class, 'bien_id', 'id_bien');
    }


}
