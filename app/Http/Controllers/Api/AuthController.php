<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /* public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'message' => 'Login correcto',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    } */

        public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        'fcm_token' => 'nullable|string',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'message' => 'Credenciales incorrectas'
        ], 401);
    }

    /** @var User $user */
    $user = Auth::user();

    // 🔥 Guardar FCM token directamente en el login
    if ($request->filled('fcm_token')) {
        $user->fcm_token = $request->fcm_token;
        $user->save();
    }

    return response()->json([
        'token' => $user->createToken('mobile')->plainTextToken,
        'user' => $user
    ]);
}
    
    /* public function updateFcmToken(Request $request)
    {
        // 1. Validar que la petición trae el token
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        try {
            // 2. Obtener el usuario autenticado a través del token de la API
            $user = Auth::user();

            // 3. Actualizar el campo y guardar en la base de datos
            $user->fcm_token = $request->fcm_token;
            $user->save();

            // 4. Devolver una respuesta exitosa
            return response()->json([
                'message' => 'FCM token updated successfully.'
            ], 200);

        } catch (\Exception $e) {
            // En caso de cualquier error, devuelve un error 500
            return response()->json([
                'message' => 'Failed to update FCM token.',
                'error' => $e->getMessage()
            ], 500);
        }
    } */
    
}
