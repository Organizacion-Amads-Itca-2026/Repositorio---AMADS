<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CategoriaTicket;
use App\Models\DetalleCompra;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DetalleCompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

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
            "*.compra_id" => "required|exists:vta_compras,id_compra",
            "*.categoria_ticket_id" => "required|exists:eve_categorias_ticket,id_categoria_ticket",
            "*.cantidad" => "required|numeric",
            "*.precio_unitario"=>"required|numeric",
            "*.subtotal"=>'required|numeric|min:0'
        ], [
            "categoria_ticket_id.exists" => "La categoria seleccionada no existe en la base de datos",
            "compra_id.exists" => "La compra no se ha registrado",
        ]);
        $items = $request->all();


        DB::beginTransaction();

        try {

            foreach ($items as $item) {

                $data = CategoriaTicket::find($item['categoria_ticket_id']);

                if ($data->cantidad_disponible < $item['cantidad']) {
                    throw new \Exception('No hay suficientes entradas');
                }

                $detalle = new DetalleCompra();
                $detalle->fill($item);
                $detalle->save();

                $data->cantidad_disponible -= $item['cantidad'];
                $data->save();
            }

            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "Detalles agregados correctamente"
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
