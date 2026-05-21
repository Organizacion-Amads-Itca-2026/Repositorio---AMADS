<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
    public function index() {
        $proveedores = DB::table('prv_proveedores')->get()->map(function($p) {
            return [
                'rowid'         => $p->id_proveedor,
                'nombreEmpresa' => $p->nombre,
                'nombreContacto'=> $p->nombre_contacto,
                'email'         => $p->email,
                'telefono'      => $p->telefono,
                'nit'           => $p->nit,
                'nrc'           => $p->nrc,
                'servicio'      => $p->servicios,
                'estado'        => $p->estado,
            ];
        });
        return response()->json($proveedores, 200);
    }

    public function show($id) {
        $p = DB::table('prv_proveedores')->where('id_proveedor', $id)->first();
        if (!$p) return response()->json(['mensaje' => 'No encontrado'], 404);
        return response()->json([
            'rowid'         => $p->id_proveedor,
            'nombreEmpresa' => $p->nombre,
            'nombreContacto'=> $p->nombre_contacto,
            'email'         => $p->email,
            'telefono'      => $p->telefono,
            'nit'           => $p->nit,
            'nrc'           => $p->nrc,
            'servicio'      => $p->servicios,
            'estado'        => $p->estado,
        ], 200);
    }

    public function porEstado($estado) {
        $p = DB::table('prv_proveedores')->where('estado', $estado)->get();
        return response()->json($p, 200);
    }

    public function store(Request $request) {
        $v = Validator::make($request->all(), [
            'nombreEmpresa' => 'nullable|string|max:150',
            'nombre'        => 'nullable|string|max:150',
            'email'         => 'nullable|email|max:80',
            'telefono'      => 'nullable|string|max:50',
            'estado'        => 'nullable|string|in:ACTIVO,INACTIVO',
        ]);
        if ($v->fails()) return response()->json(['errores' => $v->errors()], 400);
        if (!$request->nombreEmpresa && !$request->nombre) {
            return response()->json(['errores' => ['nombre' => ['El nombre es requerido']]], 400);
        }
        $id = DB::table('prv_proveedores')->insertGetId([
            'nombre'         => $request->nombreEmpresa ?? $request->nombre,
            'nombre_contacto'=> $request->nombreContacto ?? $request->nombre_contacto,
            'email'          => $request->email,
            'telefono'       => $request->telefono,
            'nit'            => $request->nit,
            'nrc'            => $request->nrc,
            'servicios'      => $request->servicio ?? $request->servicios,
            'estado'         => $request->estado ?? 'ACTIVO',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
        return response()->json(['mensaje' => 'Proveedor creado', 'rowid' => $id], 201);
    }

    public function update(Request $request, $id) {
        $p = DB::table('prv_proveedores')->where('id_proveedor', $id)->first();
        if (!$p) return response()->json(['mensaje' => 'No encontrado'], 404);
        DB::table('prv_proveedores')->where('id_proveedor', $id)->update([
            'nombre'         => $request->nombreEmpresa ?? $request->nombre ?? $p->nombre,
            'nombre_contacto'=> $request->nombreContacto ?? $request->nombre_contacto ?? $p->nombre_contacto,
            'email'          => $request->email ?? $p->email,
            'telefono'       => $request->telefono ?? $p->telefono,
            'nit'            => $request->nit ?? $p->nit,
            'nrc'            => $request->nrc ?? $p->nrc,
            'servicios'      => $request->servicio ?? $request->servicios ?? $p->servicios,
            'estado'         => $request->estado ?? $p->estado,
            'updated_at'     => now(),
        ]);
        return response()->json(['mensaje' => 'Proveedor actualizado'], 200);
    }

    public function destroy($id) {
        $p = DB::table('prv_proveedores')->where('id_proveedor', $id)->first();
        if (!$p) return response()->json(['mensaje' => 'No encontrado'], 404);
        DB::table('prv_proveedores')->where('id_proveedor', $id)->delete();
        return response()->json(['mensaje' => 'Proveedor eliminado'], 200);
    }
}