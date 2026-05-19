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
        Schema::create('eve_categorias_ticket', function (Blueprint $table) {
            $table->id('id_categoria_ticket');
            $table->unsignedBigInteger('evento_id');
            $table->string('nombre', 50);
            $table->decimal('precio_base', 16, 2);
            $table->integer('capacidad_disponible');
            $table->integer('cantidad_disponible');
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
        Schema::dropIfExists('eve_categorias_ticket');
    }
};
