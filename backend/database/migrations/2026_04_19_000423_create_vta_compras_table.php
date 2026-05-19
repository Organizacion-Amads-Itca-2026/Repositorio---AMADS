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
        Schema::create('vta_compras', function (Blueprint $table) {
            $table->id('id_compra');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('evento_id');
            $table->string('numero_factura', 50)->unique();
            $table->decimal('subtotal', 16, 2);
            $table->decimal('total_descuentos', 16, 2)->default(0);
            $table->decimal('total_pagar', 16, 2);
            $table->string('id_transaccion_pasarela', 100);
            $table->string('metodo_pago', 50);
            $table->string('codigo_qr_acceso', 500)->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id_usuario')->on('seg_usuarios');
            $table->foreign('evento_id')->references('id_evento')->on('eve_eventos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vta_compras');
    }
};
