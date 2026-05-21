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
    Schema::create('seg_usuarios', function (Blueprint $table) {
        $table->id('id_usuario');
        $table->string('nombres', 120);
        $table->string('apellidos', 120)->nullable();
        $table->string('email', 80)->unique();
        $table->binary('contrasena'); 
        $table->binary('salt');
        $table->string('telefono', 50)->nullable();
        $table->string('dui', 10)->nullable()->unique();
        $table->string('estado', 24)->default('ACTIVO');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seg_usuarios');
    }
};
