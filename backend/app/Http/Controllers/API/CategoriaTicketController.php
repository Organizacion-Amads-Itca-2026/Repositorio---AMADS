<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\CategoriaTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriaTicketController extends Controller
{
    public function showEventsCategories($idEvento)
    {
        $categorias = CategoriaTicket::where('evento_id', $idEvento)
            ->where('estado', 'ACTIVO')
            ->get();
        if ($categorias->isEmpty()) {
            return response()->json(['message' => 'Categorias no encontradas'], 403);
        }
        return response()->json($categorias, 200);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50',
            'precioBase' => 'required|numeric',
            'capacidadDisponible' => 'required|integer',
            'eventoId' => 'required|integer',
        ]);
        if ($v->fails()) return response()->json(['errores' => $v->errors()], 400);

        $cat = CategoriaTicket::create([
            'nombre' => $request->nombre,
            'precio_base' => $request->precioBase,
            'capacidad_disponible' => $request->capacidadDisponible,
            'cantidad_disponible' => $request->capacidadDisponible,
            'evento_id' => $request->eventoId,
            'estado' => 'ACTIVO',
        ]);

        return response()->json([
            'mensaje' => 'Categoría creada',
            'rowid' => $cat->id_categoria_ticket,
            'categoria' => $cat
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $cat = CategoriaTicket::find($id);
        if (!$cat) return response()->json(['mensaje' => 'No encontrada'], 404);

        $cat->update([
            'nombre' => $request->nombre ?? $cat->nombre,
            'precio_base' => $request->precioBase ?? $cat->precio_base,
            'capacidad_disponible' => $request->capacidadDisponible ?? $cat->capacidad_disponible,
            'cantidad_disponible' => $request->capacidadDisponible ?? $cat->cantidad_disponible,
            'estado' => $request->estado ?? $cat->estado,
        ]);

        return response()->json(['mensaje' => 'Categoría actualizada'], 200);
    }

    public function destroy($id)
    {
        $cat = CategoriaTicket::find($id);
        if (!$cat) return response()->json(['mensaje' => 'No encontrada'], 404);
        $cat->delete();
        return response()->json(['mensaje' => 'Categoría eliminada'], 200);
    }
}