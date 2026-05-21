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
        Schema::create('eve_lugares_evento', function (Blueprint $table) {
            $table->id('id_lugar_evento');
            $table->string('nombre', 150);
            $table->string('direccion', 255)->nullable();
            $table->string('departamento_geografico', 120);
            $table->string('municipio_geografico', 120);
            $table->integer('capacidad_maxima')->nullable();
            $table->string('estado', 24)->default('ACTIVO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eve_lugares_evento');
    }
};
