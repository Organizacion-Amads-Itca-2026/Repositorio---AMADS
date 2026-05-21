<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'vta_tickets';
    protected $primaryKey = 'id_ticket';
    public $timestamps = false;
    protected $fillable = [
        "compra_id", 
        "detalle_compra_id", 
        "numero_ticket", 
        "codigo_qr", 
        "estado"
    ];

    function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id', 'id_compra');
    }
    function detalleCompra()
    {
        return $this->belongsTo(DetalleCompra::class,'detalle_compra_id','id_detalle_compra');
    }
}
