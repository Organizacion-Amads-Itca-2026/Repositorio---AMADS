<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prv_proveedores', function (Blueprint $table) {
            $table->id('id_proveedor');
            $table->string('nombre', 150);
            $table->string('nombre_contacto', 150)->nullable();
            $table->string('email', 80)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('nit', 20)->nullable();
            $table->string('nrc', 20)->nullable();
            $table->string('servicios', 255)->nullable();
            $table->string('estado', 24)->default('ACTIVO');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('prv_proveedores');
    }
};