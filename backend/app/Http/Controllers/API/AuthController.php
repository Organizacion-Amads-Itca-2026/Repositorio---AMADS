<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario; 

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email', 
            'password' => 'required'
        ]);

        // Buscamos al usuario incluyendo su perfil
        $user = Usuario::with('perfil')->where('email', $request->email)->first();

        // Verificamos existencia y contraseña (Bcrypt)
        if (!$user || !Hash::check($request->password, $user->contrasena)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.'
            ], 401);
        }

        // Generamos el token de Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id_usuario,
                'email' => $user->email,
                'nombres' => $user->nombres,
                'apellidos' => $user->apellidos,
                // Aquí enviamos el NOMBRE del perfil 
                'perfil' => $user->perfil ? $user->perfil->nombre : 'USUARIO'
            ]
        ]);
    }

    // Cierre de sesión para destruir el token
    public function logout(Request $request)
    {
        // Borra el token actual de la base de datos
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada'
        ]);
    }

    // Registro de clientes
    public function register(Request $request)
    {
        // 1. Validar que se envie los datos mas importantes y que el correo no exista
        $request->validate([
            'nombres' => 'required|string|max:120',
            'apellidos' => 'required|string|max:120',
            'email' => 'required|email|unique:seg_usuarios,email',
            'contrasena' => 'required|string|min:8'
        ]);

        try {
            // 2. Buscamos dinámicamente el ID del perfil "USUARIO"
            $perfil = \App\Models\Perfil::where('nombre', 'USUARIO')->first();
            $perfilId = $perfil ? $perfil->id_perfil : 2; // Si no lo encuentra por alguna razón, asume 2

            // 3. Creamos el usuario
            $usuario = new Usuario();
            $usuario->nombres = $request->nombres;
            $usuario->apellidos = $request->apellidos;
            $usuario->email = $request->email;
            $usuario->contrasena = bcrypt($request->contrasena); 
            
            // Asignaciones obligatorias por seguridad
            $usuario->perfil_id = $perfilId; // Bloqueado a USUARIO
            $usuario->establecimiento_id = null; // Un cliente no tiene establecimiento
            $usuario->estado = 'ACTIVO';
            $usuario->salt = ''; // Por el detalle de tu BD que vimos antes

            $usuario->save();

            return response()->json([
                'message' => 'Cuenta creada exitosamente. Ya puedes iniciar sesión.',
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al registrar: ' . $e->getMessage()], 500);
        }
    }

    public function changePassword(Request $request)
    {
        // 1. Validamos con lo que manda el frontend
        $request->validate([
            'passwordActual' => 'required',
            'passwordNueva'  => 'required|min:8'
        ]);

        $usuario = $request->user(); // Obtenemos el usuario logueado por el token

        // 2. Verificamos que la contraseña actual sea correcta
        if (!\Illuminate\Support\Facades\Hash::check($request->passwordActual, $usuario->contrasena)) {
            return response()->json([
                'message' => 'La contraseña actual es incorrecta.'
            ], 400);
        }

        // 3. Si todo está bien, guardamos la nueva contraseña encriptada
        $usuario->contrasena = bcrypt($request->passwordNueva);
        $usuario->save();

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente.'
        ], 200);
    }
}