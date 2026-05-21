<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Descuento;

class DescuentoController extends Controller
{
    function VerificarDescuento($codigo, $idEvento)
{
    $descuento = Descuento::where('codigo', $codigo)
        ->where('estado', 'ACTIVO')
        ->whereDate('fecha_inicio', '<=', now())
        ->whereDate('fecha_fin', '>=', now())
        ->first();

    if (!$descuento) {
        return response()->json([
            'error' => 'Cupón no válido o expirado'
        ], 403);
    }

    return response()->json($descuento, 200);
}
}
