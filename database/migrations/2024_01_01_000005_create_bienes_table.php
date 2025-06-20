<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('bienes', function (Blueprint $table) {
            $table->bigIncrements('id_bien');
            $table->unsignedBigInteger('id_direccion')->nullable();
            $table->unsignedBigInteger('id_departamento')->nullable();
            $table->string('numero_inventario')->unique();
            $table->string('numero_serie')->nullable();
            $table->string('nombre');
            $table->text('descripcion_general')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fecha_adquisicion');
            $table->string('categoria');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->decimal('costo', 10, 2)->nullable(); // 👈 Agregamos el costo con 2 decimales
            $table->longText('imagen')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('resguardante_id')->nullable();
            $table->timestamps();

            $table->foreign('id_direccion')->references('id_direccion')->on('direcciones')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('id_departamento')->references('id_departamento')->on('departamentos')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('resguardante_id')->references('id_resguardante')->on('resguardantes')->onDelete('set null')->onUpdate('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('bienes');
    }
};
