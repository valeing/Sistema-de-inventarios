<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventariosFisicosTable extends Migration
{
    public function up()
    {
        Schema::create('inventarios_fisicos', function (Blueprint $table) {
            $table->id(); // Identificador único
            $table->string('nombre_inventario'); // Nombre del inventario
            $table->text('descripcion')->nullable(); // Descripción del inventario
            $table->date('fecha_inicio'); // Fecha de inicio del inventario
            $table->date('fecha_finalizacion'); // Fecha de finalización
            $table->string('estado')->default('Programado'); // Estado del inventario
            $table->boolean('mostrar_comentario')->default(false); // Indica si se debe mostrar el comentario
            $table->text('comentario')->nullable(); // Mensaje para los usuarios
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventarios_fisicos');
    }
}
