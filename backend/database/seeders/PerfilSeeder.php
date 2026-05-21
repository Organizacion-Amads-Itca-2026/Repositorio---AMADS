<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perfil;

class PerfilSeeder extends Seeder
{
    public function run()
    {
        Perfil::insert([
            [
                'nombre' => 'ADMINISTRADOR',
                'descripcion' => 'Tiene acceso total a Ventry',
                'estado' => 'ACTIVO',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'USUARIO',
                'descripcion' => 'Usuario estándar del sistema',
                'estado' => 'ACTIVO',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}