<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartamentosTable extends Migration
{
    public function up()
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->bigIncrements('id_departamento');
            $table->unsignedBigInteger('id_direccion'); // Asegurar que es `unsignedBigInteger`
            $table->string('nombre');
            $table->timestamps();

            // Clave foránea correctamente definida
            $table->foreign('id_direccion')
                ->references('id_direccion')
                ->on('direcciones')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('departamentos');
    }
}
