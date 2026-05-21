<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;
    protected $table = 'eve_eventos';
    protected $primaryKey = 'id_evento';
    public $timestamps = false;

    protected $fillable = [
        'tipo_evento_id', 
        'lugar_evento_id', 
        'nombre', 
        'descripcion', 
        'fecha_programado', 
        'hora_inicio', 
        'hora_fin', 
        'capacidad_total', 
        'foto_publicitaria', 
        'estado', 
        'version'
    ];  

    function TiposEventos()
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_evento_id', 'id_tipo_evento');
    }

    function categorias()
    {
        return $this->hasMany(CategoriaTicket::class,'evento_id','id_evento');
    }
}
