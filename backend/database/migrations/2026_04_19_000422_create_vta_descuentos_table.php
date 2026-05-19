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
        Schema::create('vta_descuentos', function (Blueprint $table) {
            $table->id('id_descuento');
            $table->unsignedBigInteger('evento_id')->nullable();
            $table->string('codigo', 50)->unique();
            $table->decimal('valor_descuento', 10, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estado', 24)->default('ACTIVO');
            $table->timestamps();

            $table->foreign('evento_id')->references('id_evento')->on('eve_eventos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vta_descuentos');
    }
};
