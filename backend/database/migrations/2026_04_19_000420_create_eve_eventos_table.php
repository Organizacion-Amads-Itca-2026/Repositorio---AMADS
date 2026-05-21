<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('eve_eventos', function (Blueprint $table) {
            $table->id('id_evento');
            $table->unsignedBigInteger('tipo_evento_id');
            $table->unsignedBigInteger('lugar_evento_id');
            $table->string('nombre', 120);
            $table->text('descripcion');
            $table->timestamp('fecha_programado');
            $table->integer('capacidad_total');
            $table->string('foto_publicitaria', 500)->nullable();
            $table->string('estado', 24)->default('ACTIVO');
            $table->bigInteger('version')->default(0);
            $table->timestamps();

        $table->foreign('tipo_evento_id')->references('id_tipo_evento')->on('eve_tipos_evento');
        $table->foreign('lugar_evento_id')->references('id_lugar_evento')->on('eve_lugares_evento');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eve_eventos');
    }
};
