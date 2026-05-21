<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Evento;
use App\Models\TipoEvento;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        TipoEvento::factory(5)->create([
             "nombre"=>"Pruebas", 
        "estado"=> "A", 
        "fecha_crea"=> now()
        ]);
        Evento::factory(5)->create([
            'tipo_evento_id' => 1,
            'lugar_evento_id' => 1,
            'nombre' => 'Evento de prueba',
            'descripcion' => 'Descripción del evento de prueba',
            'fecha_programado' => now(),
            'hora_inicio' => now(),
            'hora_fin' => now(),
            'capacidad_total' => 100,
            'foto_publicitaria' => 'ruta/a/foto.jpg',
            'estado' => 'A',
            'version' => 1
        ]);
    }
}
