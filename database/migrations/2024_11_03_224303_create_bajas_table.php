<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::create('bajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bien')->nullable()->constrained('bienes', 'id_bien')->onDelete('set null');
            $table->string('motivo');
            $table->text('descripcion_problema')->nullable();
            $table->date('fecha_baja');
            $table->longText('expediente')->nullable(); // 🔹 Se cambia a longText para evitar errores
            $table->string('mime_type_expediente')->nullable(); // 🔹 Se almacena el tipo MIME del PDF
            $table->string('nombre_apellido')->nullable();
            $table->string('categoria')->nullable();
            $table->string('estado')->nullable();
            $table->string('departamento')->nullable();
            $table->string('numero_inventario')->nullable();
            $table->string('numero_serie')->nullable();
            $table->string('nombre')->nullable();
            $table->text('descripcion_general')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->binary('imagen')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();
        });


        // Cambiar el tipo de las columnas `expediente` y `imagen` a LONGBLOB
        Schema::table('bajas', function (Blueprint $table) {
            DB::statement('ALTER TABLE bajas MODIFY expediente LONGBLOB');
            DB::statement('ALTER TABLE bajas MODIFY imagen LONGBLOB');
        });

    }

    public function down()
    {
        Schema::dropIfExists('bajas');
    }
};
