<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'vta_compras';
    protected $primaryKey = 'id_compra';
    protected $fillable = [
        //"id_compra",
         "usuario_id", 
         "evento_id", 
         "numero_factura", 
         "subtotal", 
         "total_descuentos", 
         "total_pagar", 
         "id_transaccion_pasarela", 
         "metodo_pago", 
         "codigo_qr_acceso"
    ];

    function detallesCompra()
    {
        return $this->hasMany(DetalleCompra::class, 'compra_id', 'id_compra');
    }
     function eventos()
     {
        return $this->belongsTo(Evento::class, "evento_id", "id_evento");
    }
     /*function usuarios():BelongsTo{
        return $this->belongsTo(Usuario::class, "evento_id", "id_evento");
    }*/
}
