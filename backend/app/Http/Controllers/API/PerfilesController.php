<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerfilesController extends Controller
{
    public function index()
    {
        // Retornar perfiles desde la nueva tabla seg_perfiles
        $perfiles = DB::table('seg_perfiles')->get()->map(function($p) {
            return [
                'rowid' => $p->id_perfil,
                'nombre' => $p->nombre
            ];
        });
        return response()->json($perfiles, 200);
    }
}
