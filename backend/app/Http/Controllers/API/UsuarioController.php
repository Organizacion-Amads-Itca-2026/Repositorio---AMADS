<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    /**
     * Listar todos los usuarios (administrativo).
     */
    public function index()
    {
        $usuarios = Usuario::all()->map(function($user) {
            return [
                'rowid' => $user->id_usuario,
                'nombres' => $user->nombres,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'perfil' => $user->perfil_id, // Por ahora el ID, el frontend puede mapear o podemos cargar el nombre
                'establecimiento' => $user->establecimiento_id,
                'estado' => $user->estado // <-- ¡ESTA ES LA LÍNEA MÁGICA!
            ];
        });
        return response()->json($usuarios, 200);
    }

    /**
     * Mostrar un usuario específico.
     */
    public function show($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }
        
        $data = [
            'rowid' => $usuario->id_usuario,
            'nombres' => $usuario->nombres,
            'apellidos' => $usuario->apellidos,
            'email' => $usuario->email,
            'perfil' => $usuario->perfil_id,
            'establecimiento' => $usuario->establecimiento_id,
            'estado' => $usuario->estado
        ];

        return response()->json($data, 200);
    }

    /**
     * Crear un nuevo usuario (desde administración).
     */
public function store(Request $request)
    {
        try {
            $usuario = new Usuario();
            $usuario->nombres = $request->nombres;
            $usuario->apellidos = $request->apellidos;
            $usuario->email = $request->email;
            $usuario->contrasena = bcrypt($request->contrasena); 
            $usuario->perfil_id = $request->perfil_id; 
            $usuario->establecimiento_id = $request->establecimiento_id ?? null;
            $usuario->salt = ''; // Le mandamos un texto vacío para que no falle

            $usuario->save();


            return response()->json([
                'message' => 'Usuario creado con éxito', 
                'usuario' => $usuario
            ], 201);

        } catch (\Exception $e) {
            // Esto le devolverá el error exacto a la consola para no adivinar
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar datos de un usuario.
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombres' => 'string|max:120',
            'apellidos' => 'nullable|string|max:120',
            'email' => 'email|max:80|unique:seg_usuarios,email,' . $id . ',id_usuario',
            'perfil' => 'nullable|integer',
            'establecimiento' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()], 400);
        }

        $data = $request->only(['nombres', 'apellidos', 'email', 'telefono', 'dui', 'estado']);
        if ($request->has('perfil')) $data['perfil_id'] = $request->perfil;
        if ($request->has('establecimiento')) $data['establecimiento_id'] = $request->establecimiento;

        $usuario->update($data);

        return response()->json(['mensaje' => 'Usuario actualizado exitosamente', 'usuario' => $usuario], 200);
    }

    /**
     * Eliminar (o desactivar) un usuario.
     */
    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        // Podríamos hacer borrado lógico o físico según prefiera el equipo.
        // Por ahora, borrado físico para cumplir con el CRUD estándar.
        $usuario->delete();

        return response()->json(['mensaje' => 'Usuario eliminado exitosamente'], 200);
    }
}
