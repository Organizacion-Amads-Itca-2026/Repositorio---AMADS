<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table = 'vta_detalle_compra';
    protected $primaryKey = 'id_detalle_compra';
    protected $fillable = [
        //"id_detalle_compra", 
        "compra_id",
        "categoria_ticket_id", 
        "cantidad", 
        "precio_unitario", 
        "subtotal"
    ];

    function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id', 'id_compra');
    }
    function categoriaTicket()
    {
        return $this->belongsTo(CategoriaTicket::class,'categoria_ticket_id','id_categoria_ticket');
    }
}
