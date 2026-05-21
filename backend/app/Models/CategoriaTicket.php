<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaTicket extends Model
{
    protected $table = 'eve_categorias_ticket';
    protected $primaryKey = 'id_categoria_ticket';
    protected $fillable = [
        "evento_id", 
        "nombre", 
        "precio_base", 
        "capacidad_disponible", 
        "cantidad_disponible", 
        "estado"
    ];

    function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id', 'id_evento');
    }
    function tickets()
    {
        return $this->hasMany(DetalleCompra::class,'categoria_ticket_id','id_categoria_ticket');
    }
}
