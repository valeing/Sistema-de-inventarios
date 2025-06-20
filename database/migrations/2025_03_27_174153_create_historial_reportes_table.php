<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historial_reportes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporte_id')->nullable(); // opcional
            $table->unsignedBigInteger('resguardante_id');
            $table->unsignedBigInteger('bien_id');

            $table->text('comentario'); // comentario original del reporte
            $table->text('comentario_rechazo')->nullable(); // nuevo campo opcional

            $table->date('fecha_reporte');
            $table->string('estatus'); // cancelado o rechazado
            $table->timestamp('fecha_eliminacion');
            $table->timestamps();

            $table->foreign('resguardante_id')->references('id_resguardante')->on('resguardantes')->onDelete('cascade');
            $table->foreign('bien_id')->references('id_bien')->on('bienes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_reportes');
    }
};
