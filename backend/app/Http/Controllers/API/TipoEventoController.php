<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoEventoController extends Controller
{
    /**
     * Listar todas las categorías (tipos de evento).
     */
    public function index()
    {
        $tiposEvento = TipoEvento::all();
        return response()->json($tiposEvento, 200);
    }

    /**
     * Crear una nueva categoría.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:eve_tipos_evento,nombre',
            'estado' => 'nullable|string|in:ACTIVO,INACTIVO'
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()], 400);
        }

        $tipoEvento = TipoEvento::create($request->all());

        return response()->json(['mensaje' => 'Categoría creada exitosamente', 'tipoEvento' => $tipoEvento], 201);
    }

    /**
     * Actualizar una categoría.
     */
    public function update(Request $request, $id)
    {
        $tipoEvento = TipoEvento::find($id);
        if (!$tipoEvento) {
            return response()->json(['mensaje' => 'Categoría no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:100|unique:eve_tipos_evento,nombre,' . $id . ',id_tipo_evento',
            'estado' => 'string|in:ACTIVO,INACTIVO'
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()], 400);
        }

        $tipoEvento->update($request->all());

        return response()->json(['mensaje' => 'Categoría actualizada exitosamente', 'tipoEvento' => $tipoEvento], 200);
    }

    /**
     * Eliminar una categoría.
     */
    public function destroy($id)
    {
        $tipoEvento = TipoEvento::find($id);
        if (!$tipoEvento) {
            return response()->json(['mensaje' => 'Categoría no encontrada'], 404);
        }

        $tipoEvento->delete();

        return response()->json(['mensaje' => 'Categoría eliminada exitosamente'], 200);
    }
}
