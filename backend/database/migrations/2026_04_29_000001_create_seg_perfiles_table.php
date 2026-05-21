<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seg_perfiles', function (Blueprint $table) {
            $table->id('id_perfil');
            $table->string('nombre', 50)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->string('estado', 20)->default('ACTIVO');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seg_perfiles');
    }
};
