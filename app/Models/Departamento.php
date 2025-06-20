<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';

    protected $primaryKey = 'id_departamento';

    protected $fillable = ['nombre', 'id_direccion'];

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion');
    }

    // Relación con Bienes
    public function bienes()
    {
        return $this->hasMany(Bien::class, 'id_departamento');
    }
}
