<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoEvento extends Model
{
    use HasFactory;
    protected $table = 'eve_tipos_evento';
    protected $primaryKey = 'id_tipo_evento';
    public $timestamps = false;
    protected $fillable = [
        "nombre", 
        "estado", 
        "fecha_crea"
    ];
    function eventos():HasMany
    {
        return $this->hasMany(Evento::class, 'tipo_evento_id', 'id_tipo_evento');
    }

}
