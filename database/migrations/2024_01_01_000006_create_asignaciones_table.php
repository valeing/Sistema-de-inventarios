<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsignacionesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id('id_asignacion'); // Clave primaria de la tabla asignaciones
            $table->unsignedBigInteger('id_bien'); // Relación con la tabla bienes
            $table->unsignedBigInteger('id_resguardante'); // Relación con la tabla resguardantes
            $table->date('fecha_asignacion'); // Fecha en que se asigna el bien
            $table->timestamps();

            // Definir las relaciones de claves foráneas
            $table->foreign('id_bien')->references('id_bien')->on('bienes')->onDelete('cascade');
            $table->foreign('id_resguardante')->references('id_resguardante')->on('resguardantes')->onDelete('cascade');

            // Asegurar que la combinación de bien y resguardante sea única
            $table->unique(['id_bien', 'id_resguardante'], 'bien_resguardante_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('asignaciones');
    }
}
