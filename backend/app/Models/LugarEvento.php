<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LugarEvento extends Model
{
    use HasFactory;

    protected $table = 'eve_lugares_evento';
    protected $primaryKey = 'id_lugar_evento';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'direccion',
        'departamento_geografico',
        'municipio_geografico',
        'capacidad_maxima',
        'estado',
    ];

    function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'lugar_evento_id', 'id_lugar_evento');
    }
}
