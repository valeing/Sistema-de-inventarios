<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resguardante extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_resguardante';
    protected $table = 'resguardantes';

    protected $fillable = [
        'numero_empleado',
        'nombre_apellido',
        'fecha',
        'id_direccion',
        'id_departamento',
        'telefono',
        'estado',
        'user_id',
    ];

    /**
     * Relación con Asignaciones.
     */
    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class, 'id_resguardante', 'id_resguardante');
    }

    /**
     * Relación con Dirección.
     */
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion');
    }

    /**
     * Relación con Departamento.
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento', 'id_departamento');
    }


    /**
     * Relación con Usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    /**
     * Relación directa con Bienes.
     */
    public function bienes()
    {
        return $this->hasMany(Bien::class, 'resguardante_id', 'id_resguardante');
    }

    public function reportes()
    {
        return $this->hasMany(ReporteBien::class, 'resguardante_id', 'id_resguardante');
    }

    public function reportesHistorial()
    {
        return $this->hasMany(\App\Models\HistorialReporte::class, 'resguardante_id');
    }


}
