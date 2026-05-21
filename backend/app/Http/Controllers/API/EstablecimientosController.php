<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstablecimientosController extends Controller
{
    public function index()
    {
        $establecimientos = DB::table('adm_establecimientos')->get()->map(function($e) {
            return [
                'rowid' => $e->id_establecimiento,
                'nombre' => $e->nombre
            ];
        });
        return response()->json($establecimientos, 200);
    }
}
