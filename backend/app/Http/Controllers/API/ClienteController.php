<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function index() {
        $clientes = DB::table('vta_clientes')->get()->map(function($c) {
            return [
                'rowid'     => $c->id_cliente,
                'nombres'   => $c->nombres,
                'apellidos' => $c->apellidos,
                'email'     => $c->email,
                'telefono'  => $c->telefono,
                'dui'       => $c->dui,
                'estado'    => $c->estado,
            ];
        });
        return response()->json($clientes, 200);
    }

    public function show($id) {
        $c = DB::table('vta_clientes')->where('id_cliente', $id)->first();
        if (!$c) return response()->json(['mensaje' => 'No encontrado'], 404);
        return response()->json([
            'rowid'     => $c->id_cliente,
            'nombres'   => $c->nombres,
            'apellidos' => $c->apellidos,
            'email'     => $c->email,
            'telefono'  => $c->telefono,
            'dui'       => $c->dui,
            'estado'    => $c->estado,
        ], 200);
    }

    public function porEstado($estado) {
        $c = DB::table('vta_clientes')->where('estado', $estado)->get();
        return response()->json($c, 200);
    }

    public function store(Request $request) {
        $v = Validator::make($request->all(), [
            'nombres' => 'required|string|max:120',
            'email'   => 'required|email|max:80|unique:vta_clientes,email',
            'dui'     => 'nullable|string|max:10|unique:vta_clientes,dui',
        ]);
        if ($v->fails()) return response()->json(['errores' => $v->errors()], 400);
        $id = DB::table('vta_clientes')->insertGetId([
            'nombres'    => $request->nombres,
            'apellidos'  => $request->apellidos,
            'email'      => $request->email,
            'telefono'   => $request->telefono,
            'dui'        => $request->dui,
            'estado'     => $request->estado ?? 'ACTIVO',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['mensaje' => 'Cliente creado', 'rowid' => $id], 201);
    }

    public function update(Request $request, $id) {
        $c = DB::table('vta_clientes')->where('id_cliente', $id)->first();
        if (!$c) return response()->json(['mensaje' => 'No encontrado'], 404);
        DB::table('vta_clientes')->where('id_cliente', $id)->update([
            'nombres'    => $request->nombres ?? $c->nombres,
            'apellidos'  => $request->apellidos ?? $c->apellidos,
            'email'      => $request->email ?? $c->email,
            'telefono'   => $request->telefono ?? $c->telefono,
            'dui'        => $request->dui ?? $c->dui,
            'estado'     => $request->estado ?? $c->estado,
            'updated_at' => now(),
        ]);
        return response()->json(['mensaje' => 'Cliente actualizado'], 200);
    }

    public function destroy($id) {
        $c = DB::table('vta_clientes')->where('id_cliente', $id)->first();
        if (!$c) return response()->json(['mensaje' => 'No encontrado'], 404);
        DB::table('vta_clientes')->where('id_cliente', $id)->delete();
        return response()->json(['mensaje' => 'Cliente eliminado'], 200);
    }
}