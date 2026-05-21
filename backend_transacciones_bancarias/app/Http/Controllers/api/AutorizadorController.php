<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutorizadorController extends Controller
{
    function autorizarTransaccion(Request $request)
    {
        $data = $request->validate([
            'monto' => 'required|numeric',
            'cuenta_origen' => 'required|string',
            //'cuenta_destino' => 'required|string',
        ]);

        // Lógica de autorización (simulada)
        if ($data['monto'] > 2350) {
            return response()->json(['message' => 'Transacción no autorizada: monto excede el límite.'], 200);
        }

        return response()->json(['message' => 'Transacción autorizada.'], 200);
    }
}
