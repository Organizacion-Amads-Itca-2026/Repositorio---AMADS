<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'seg_perfiles'; 
    protected $primaryKey = 'id_perfil';
    
    
    public $timestamps = true; 

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'perfil_id', 'id_perfil');
    }
}
