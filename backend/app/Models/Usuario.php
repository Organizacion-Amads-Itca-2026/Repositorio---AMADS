<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = "seg_usuarios";
    protected $primaryKey = 'id_usuario';

    public $timestamps = true; 

    protected $fillable = [
        'nombres',
        'apellidos',
        'email',
        'contrasena',
        'perfil_id',          
        'establecimiento_id'  
    ];

    // Indica a Laravel que la columna de contraseña es "contrasena"
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Relación con el Perfil (Escenario B)
    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'perfil_id', 'id_perfil');
    }
}