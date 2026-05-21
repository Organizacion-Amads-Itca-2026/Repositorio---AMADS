<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adm_establecimientos', function (Blueprint $table) {
            $table->id('id_establecimiento');
            $table->string('nombre', 100)->unique();
            $table->string('direccion', 255)->nullable();
            $table->string('estado', 20)->default('ACTIVO');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adm_establecimientos');
    }
};
