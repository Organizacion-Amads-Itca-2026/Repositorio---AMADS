<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vta_clientes', function (Blueprint $table) {
            $table->id('id_cliente');
            $table->string('nombres', 120);
            $table->string('apellidos', 120)->nullable();
            $table->string('email', 80)->unique();
            $table->string('telefono', 50)->nullable();
            $table->string('dui', 10)->nullable()->unique();
            $table->string('estado', 24)->default('ACTIVO');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('vta_clientes');
    }
};