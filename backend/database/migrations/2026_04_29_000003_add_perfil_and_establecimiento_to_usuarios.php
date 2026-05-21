<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seg_usuarios', function (Blueprint $table) {
            $table->unsignedBigInteger('perfil_id')->nullable()->after('dui');
            $table->unsignedBigInteger('establecimiento_id')->nullable()->after('perfil_id');
            
            $table->foreign('perfil_id')->references('id_perfil')->on('seg_perfiles');
            $table->foreign('establecimiento_id')->references('id_establecimiento')->on('adm_establecimientos');
        });
    }

    public function down(): void
    {
        Schema::table('seg_usuarios', function (Blueprint $table) {
            $table->dropForeign(['perfil_id']);
            $table->dropForeign(['establecimiento_id']);
            $table->dropColumn(['perfil_id', 'establecimiento_id']);
        });
    }
};
