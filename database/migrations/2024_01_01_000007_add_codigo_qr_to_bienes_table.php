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
        Schema::table('bienes', function (Blueprint $table) {
            $table->text('codigo_qr')->nullable(); // Columna para almacenar el código QR en base64
        });
    }

    public function down()
    {
        Schema::table('bienes', function (Blueprint $table) {
            $table->dropColumn('codigo_qr'); // Eliminar la columna en caso de revertir la migración
        });
    }
};
