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
        Schema::create('vta_detalle_compra', function (Blueprint $table) {
            $table->id('id_detalle_compra');
            $table->unsignedBigInteger('compra_id');
            $table->unsignedBigInteger('categoria_ticket_id');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 16, 2);
            $table->decimal('subtotal', 16, 2);
            $table->timestamps();

            $table->foreign('compra_id')->references('id_compra')->on('vta_compras');
            $table->foreign('categoria_ticket_id')->references('id_categoria_ticket')->on('eve_categorias_ticket');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vta_detalle_compra');
    }
};
