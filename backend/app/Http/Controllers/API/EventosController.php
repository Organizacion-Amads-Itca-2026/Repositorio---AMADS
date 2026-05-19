<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Usuario;
use App\Models\TipoEvento;
use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventosController extends Controller
{
    public function index()
    {
        $eventos = Evento::where("estado", "ACTIVO")->get();

        $mapped = $eventos->map(fn($e) => [
            'rowid'            => $e->id_evento,
            'nombre'           => $e->nombre,
            'descripcion'      => $e->descripcion,
            'tipoEvento'       => $e->tipo_evento_id,
            'lugarEvento'      => $e->lugar_evento_id,
            'fechaProgramado'  => $e->fecha_programado,
            'capacidadTotal'   => $e->capacidad_total,
            'fotoPublicitaria' => $e->foto_publicitaria,
            'estado'           => $e->estado,
        ]);

        return response()->json($mapped, 200);
    }

    public function show($id)
    {
        $evento = Evento::find($id);
        if (!$evento) {
            return response()->json(['mensaje' => 'Evento no encontrado'], 404);
        }
        return response()->json($evento, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:120',
            'descripcion' => 'required|string',
            'tipo_evento_id' => 'required|exists:eve_tipos_evento,id_tipo_evento',
            'lugar_evento_id' => 'required|exists:eve_lugares_evento,id_lugar_evento',
            'fecha_programado' => 'required|date',
            'capacidad_total' => 'required|integer|min:1',
            'estado' => 'nullable|string|in:ACTIVO,INACTIVO,CANCELADO'
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()], 400);
        }

        $evento = Evento::create($request->all());

        return response()->json([
            'mensaje' => 'Evento creado exitosamente',
            'rowid' => $evento->id_evento,
            'evento' => $evento
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::find($id);
        if (!$evento) {
            return response()->json(['mensaje' => 'Evento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:120',
            'tipo_evento_id' => 'exists:eve_tipos_evento,id_tipo_evento',
            'lugar_evento_id' => 'exists:eve_lugares_evento,id_lugar_evento',
            'fecha_programado' => 'date',
            'capacidad_total' => 'integer|min:1',
            'estado' => 'string|in:ACTIVO,INACTIVO,CANCELADO'
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()], 400);
        }

        $evento->update($request->all());

        return response()->json(['mensaje' => 'Evento actualizado exitosamente', 'evento' => $evento], 200);
    }

    public function destroy($id)
    {
        $evento = Evento::find($id);
        if (!$evento) {
            return response()->json(['mensaje' => 'Evento no encontrado'], 404);
        }

        $evento->delete();

        return response()->json(['mensaje' => 'Evento eliminado exitosamente'], 200);
    }

    public function metrics()
    {
        $totalUsuarios = Usuario::count();
        $totalEventos = Evento::count();
        $totalCategorias = TipoEvento::count();
        $totalVentas = Compra::sum('total_pagar');
        $totalTicketsVendidos = Compra::count();

        return response()->json([
            'totalUsuarios' => $totalUsuarios,
            'totalEventos' => $totalEventos,
            'totalCategorias' => $totalCategorias,
            'totalVentas' => (float)$totalVentas,
            'totalTicketsVendidos' => $totalTicketsVendidos
        ], 200);
    }

    public function subirFoto(Request $request, $id)
    {
        $evento = Evento::find($id);
        if (!$evento) return response()->json(['mensaje' => 'Evento no encontrado'], 404);

        if ($request->hasFile('file')) {
            $archivo = $request->file('file');
            $nombre = 'evento_' . $id . '_' . time() . '.' . $archivo->getClientOriginalExtension();
            $archivo->storeAs('public/eventos', $nombre);
            $evento->foto_publicitaria = '/storage/eventos/' . $nombre;
            $evento->save();
            return response()->json(['mensaje' => 'Foto subida', 'url' => $evento->foto_publicitaria], 200);
        }
        return response()->json(['mensaje' => 'No se recibió archivo'], 400);
    }

    public function eliminarFoto($id)
    {
        $evento = Evento::find($id);
        if (!$evento) return response()->json(['mensaje' => 'Evento no encontrado'], 404);
        $evento->foto_publicitaria = null;
        $evento->save();
        return response()->json(['mensaje' => 'Foto eliminada'], 200);
    }
}