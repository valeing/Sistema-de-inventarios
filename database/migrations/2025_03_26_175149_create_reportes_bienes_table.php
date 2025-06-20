<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reportes_bienes', function (Blueprint $table) {
            $table->id(); // ID del reporte

            // Relaciones
            $table->unsignedBigInteger('resguardante_id'); // FK a resguardantes
            $table->unsignedBigInteger('bien_id');         // FK a bienes

            // Datos del reporte
            $table->text('comentario');                    // Comentario inicial
            $table->date('fecha_reporte')->default(DB::raw('CURRENT_DATE')); // Fecha por defecto
            $table->enum('estatus', ['en proceso', 'completado', 'rechazado', 'cancelado'])->default('en proceso');

            // Nuevo campo opcional para comentarios de rechazo
            $table->text('comentario_rechazo')->nullable();

            $table->timestamps();

            // Claves foráneas
            $table->foreign('resguardante_id')->references('id_resguardante')->on('resguardantes')->onDelete('cascade');
            $table->foreign('bien_id')->references('id_bien')->on('bienes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('reportes_bienes');
    }
};
