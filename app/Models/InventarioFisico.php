<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioFisico extends Model
{
    use HasFactory;

    protected $table = 'inventarios_fisicos';

    // Atributos asignables
    protected $fillable = [
        'nombre_inventario',
        'descripcion',
        'fecha_inicio',
        'fecha_finalizacion',
        'estado',
        'mostrar_comentario',
        'comentario',
    ];
}
