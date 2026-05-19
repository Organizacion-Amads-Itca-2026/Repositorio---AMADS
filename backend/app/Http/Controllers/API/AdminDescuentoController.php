<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminDescuentoController extends Controller
{
    public function index() {
        $descuentos = DB::table('vta_descuentos')->get()->map(function($d) {
            return [
                'rowid'          => $d->id_descuento,
                'codigo'         => $d->codigo,
                'valorDescuento' => $d->valor_descuento,
                'fechaInicio'    => $d->fecha_inicio,
                'fechaFin'       => $d->fecha_fin,
                'estado'         => $d->estado,
            ];
        });
        return response()->json($descuentos, 200);
    }

    public function show($id) {
        $d = DB::table('vta_descuentos')->where('id_descuento', $id)->first();
        if (!$d) return response()->json(['mensaje' => 'No encontrado'], 404);
        return response()->json([
            'rowid'          => $d->id_descuento,
            'codigo'         => $d->codigo,
            'valorDescuento' => $d->valor_descuento,
            'fechaInicio'    => $d->fecha_inicio,
            'fechaFin'       => $d->fecha_fin,
            'estado'         => $d->estado,
        ], 200);
    }

    public function porCodigo($codigo) {
        $d = DB::table('vta_descuentos')->where('codigo', $codigo)->first();
        if (!$d) return response()->json(['mensaje' => 'No encontrado'], 404);
        return response()->json($d, 200);
    }

    public function porEstado($estado) {
        return response()->json(DB::table('vta_descuentos')->where('estado', $estado)->get(), 200);
    }

    public function vigentes() {
        $d = DB::table('vta_descuentos')
            ->where('estado', 'ACTIVO')
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->get();
        return response()->json($d, 200);
    }

    public function disponibles() {
        $d = DB::table('vta_descuentos')
            ->where('estado', 'ACTIVO')
            ->where('fecha_fin', '>=', now())
            ->get();
        return response()->json($d, 200);
    }

    public function store(Request $request) {
        $v = Validator::make($request->all(), [
            'codigo' => 'required|string|max:50|unique:vta_descuentos,codigo',
        ]);
        if ($v->fails()) return response()->json(['errores' => $v->errors()], 400);

        $id = DB::table('vta_descuentos')->insertGetId([
            'evento_id'       => $request->eventoId ?? $request->evento_id ?? null,
            'codigo'          => $request->codigo,
            'valor_descuento' => $request->valorDescuento ?? $request->valor_descuento ?? 0,
            'fecha_inicio'    => $request->fechaInicio ?? $request->fecha_inicio,
            'fecha_fin'       => $request->fechaFin ?? $request->fecha_fin,
            'estado'          => $request->estado ?? 'ACTIVO',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
        return response()->json(['mensaje' => 'Descuento creado', 'rowid' => $id], 201);
    }

    public function update(Request $request, $id) {
        $d = DB::table('vta_descuentos')->where('id_descuento', $id)->first();
        if (!$d) return response()->json(['mensaje' => 'No encontrado'], 404);
        DB::table('vta_descuentos')->where('id_descuento', $id)->update([
            'codigo'          => $request->codigo ?? $d->codigo,
            'valor_descuento' => $request->valorDescuento ?? $request->valor_descuento ?? $d->valor_descuento,
            'fecha_inicio'    => $request->fechaInicio ?? $request->fecha_inicio ?? $d->fecha_inicio,
            'fecha_fin'       => $request->fechaFin ?? $request->fecha_fin ?? $d->fecha_fin,
            'estado'          => $request->estado ?? $d->estado,
            'updated_at'      => now(),
        ]);
        return response()->json(['mensaje' => 'Descuento actualizado'], 200);
    }

    public function usar($id) {
        $d = DB::table('vta_descuentos')->where('id_descuento', $id)->first();
        if (!$d) return response()->json(['mensaje' => 'No encontrado'], 404);
        return response()->json(['mensaje' => 'Descuento usado', 'descuento' => $d], 200);
    }

    public function destroy($id) {
        $d = DB::table('vta_descuentos')->where('id_descuento', $id)->first();
        if (!$d) return response()->json(['mensaje' => 'No encontrado'], 404);
        DB::table('vta_descuentos')->where('id_descuento', $id)->delete();
        return response()->json(['mensaje' => 'Descuento eliminado'], 200);
    }
}