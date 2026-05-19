<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    protected $table = 'vta_descuentos';

    protected $fillable = [
         "evento_id", 
         "codigo", 
         "valor_descuento", 
         "fecha_inicio",
          "fecha_fin", 
          "estado"
    ];
    protected $casts = [
    'fecha_inicio' => 'datetime',
    'fecha_fin' => 'datetime',
    ];
    public function eventos()
    {
        return $this->belongsTo(Evento::class, 'evento_id', 'id_evento');
    }
}
