<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\Usuario;
use Exception;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "usuario_id" => "required|exists:seg_usuarios,id_usuario",
            "evento_id" => "required|exists:eve_eventos,id_evento",
            "numero_factura" => ["required", "regex:/^NC\d{4}$/"],
            "subtotal" => "required|numeric",
            "total_descuentos" => "nullable|numeric",
            "total_pagar" => "required|numeric",
            "id_transaccion_pasarela" => "string",
            "metodo_pago" => "required|string|max:50",
        ], [
            "usuario_id.exists" => "El usuario no existe en la base de datos",
            "evento_id.exists" => "El evento no existe en la base de datos",
            "numero_compra.regex" => "El número de compra debe tener formato NC0000",
            "subtotal.required" => "El campo subtotal es requerido",
            "total_descuentos.numeric" => "El campo descuentos debe ser un campo numérico",
            "total_pagar.required" => "El campo total es requerido",
            "subtotal" => "El campo subtotal debe ser un campo numérico",
            "total_pagar" => "El campo total debe ser un campo numérico",
            "metodo_pago.required" => "El campo metodo de pago es requerido",

        ]);
        try {
            $data = new Compra();
            $data->fill($request->only([
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
            ]));
            $isOk = $data->save();
            if (!$isOk) {
                return response()->json(['error' => 'Error al realizar la compra'], 403);
            }
            return response()->json([
                "success" => true,
                "message" => "Compra realizada",
                "id" => $data->id_compra
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Contacta a soporte técnico' . $e], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $usuario = Usuario::with('compras')->find($id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario->compras, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
