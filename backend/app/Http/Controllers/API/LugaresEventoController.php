<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LugarEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LugaresEventoController extends Controller
{
    public function index()
    {
        $lugares = LugarEvento::all();

        $mapped = $lugares->map(fn($l) => [
            'rowid'                  => $l->id_lugar_evento,
            'nombre'                 => $l->nombre,
            'direccion'              => $l->direccion,
            'departamentoGeografico' => $l->departamento_geografico,
            'municipioGeografico'    => $l->municipio_geografico,
            'capacidadMaxima'        => $l->capacidad_maxima,
            'estado'                 => $l->estado,
        ]);

        return response()->json($mapped, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'                 => 'required|string|max:150',
            'direccion'              => 'nullable|string|max:255',
            'departamentoGeografico' => 'required|string|max:120',
            'municipioGeografico'    => 'required|string|max:120',
            'capacidadMaxima'        => 'nullable|integer|min:1',
            'estado'                 => 'nullable|string|in:ACTIVO,INACTIVO',
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()], 400);
        }

        $lugar = LugarEvento::create([
            'nombre'                  => $request->nombre,
            'direccion'               => $request->direccion,
            'departamento_geografico' => $request->departamentoGeografico,
            'municipio_geografico'    => $request->municipioGeografico,
            'capacidad_maxima'        => $request->capacidadMaxima,
            'estado'                  => $request->estado ?? 'ACTIVO',
        ]);

        return response()->json([
            'mensaje' => 'Lugar creado exitosamente',
            'lugar'   => [
                'rowid'                  => $lugar->id_lugar_evento,
                'nombre'                 => $lugar->nombre,
                'direccion'              => $lugar->direccion,
                'departamentoGeografico' => $lugar->departamento_geografico,
                'municipioGeografico'    => $lugar->municipio_geografico,
                'capacidadMaxima'        => $lugar->capacidad_maxima,
                'estado'                 => $lugar->estado,
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $lugar = LugarEvento::find($id);
        if (!$lugar) {
            return response()->json(['mensaje' => 'Lugar no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre'                 => 'nullable|string|max:150',
            'direccion'              => 'nullable|string|max:255',
            'departamentoGeografico' => 'nullable|string|max:120',
            'municipioGeografico'    => 'nullable|string|max:120',
            'capacidadMaxima'        => 'nullable|integer|min:1',
            'estado'                 => 'nullable|string|in:ACTIVO,INACTIVO',
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()], 400);
        }

        $lugar->update([
            'nombre'                  => $request->nombre ?? $lugar->nombre,
            'direccion'               => $request->direccion ?? $lugar->direccion,
            'departamento_geografico' => $request->departamentoGeografico ?? $lugar->departamento_geografico,
            'municipio_geografico'    => $request->municipioGeografico ?? $lugar->municipio_geografico,
            'capacidad_maxima'        => $request->capacidadMaxima ?? $lugar->capacidad_maxima,
            'estado'                  => $request->estado ?? $lugar->estado,
        ]);

        return response()->json(['mensaje' => 'Lugar actualizado exitosamente'], 200);
    }

    public function destroy($id)
    {
        $lugar = LugarEvento::find($id);
        if (!$lugar) {
            return response()->json(['mensaje' => 'Lugar no encontrado'], 404);
        }

        $lugar->delete();

        return response()->json(['mensaje' => 'Lugar eliminado exitosamente'], 200);
    }
}
