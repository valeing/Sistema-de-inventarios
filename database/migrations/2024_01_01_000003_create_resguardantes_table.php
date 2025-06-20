<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('resguardantes', function (Blueprint $table) {
            $table->bigIncrements('id_resguardante'); // ID primario
            $table->string('numero_empleado')->unique(); // Número de empleado único
            $table->string('nombre_apellido'); // Nombre completo
            $table->date('fecha'); // Fecha de creación o asignación
            $table->unsignedBigInteger('id_direccion'); // Relación con 'direcciones'
            $table->unsignedBigInteger('id_departamento'); // Relación con 'departamentos'
            $table->unsignedBigInteger('user_id')->nullable(); // Relación opcional con 'users'
            $table->string('telefono')->nullable(); // Teléfono opcional
            $table->enum('estado', ['activo', 'inactivo'])->default('activo'); // Estado con valor predeterminado
            $table->timestamps(); // Timestamps (created_at, updated_at)

            // Llaves foráneas
            $table->foreign('id_direccion')
                ->references('id_direccion')->on('direcciones')
                ->onDelete('restrict') // No permite eliminar la dirección si está en uso
                ->onUpdate('cascade');

            $table->foreign('id_departamento')
                ->references('id_departamento')->on('departamentos')
                ->onDelete('restrict') // No permite eliminar el departamento si está en uso
                ->onUpdate('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('set null'); // Establece 'user_id' como null si el usuario se elimina
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('resguardantes');
    }
};
