<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\NotificacionController;
use App\Http\Controllers\Api\AuthController; // Asegúrate de apuntar al controlador correcto
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', function(Request $request) {
    // 1. Validar datos, incluyendo el fcm_token opcional
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'fcm_token' => 'nullable|string', // Tu excelente idea de enviarlo aquí
    ]);

    // 2. Buscar usuario y verificar contraseña
    $user = App\Models\User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    // 3. Guardar el FCM token si se envió
    if ($request->filled('fcm_token')) {
        $user->fcm_token = $request->fcm_token;
        $user->save();
    }

    // 4. Crear el token de autenticación de Sanctum
    $apiToken = $user->createToken('auth_token')->plainTextToken;

    // 5. Devolver TODOS los datos que necesitas, MANTENIENDO TU ESTRUCTURA PLANA
    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'sucursal' => $user->sucursal,
        'userid' => $user->userid,
        'clienteid' => $user->clienteid,
        'token' => $apiToken, // <-- ¡AÑADIMOS EL TOKEN QUE FLUTTER NECESITA!
    ]);
});

// REGISTRO
Route::post('/register', function(Request $request) {
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6'
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password)
    ]);

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email
    ]);
});

Route::get('/areas', function(Request $request) {
    $tipo = $request->query('tipo');
    $sucursal = $request->query('sucursal');

    return DB::table('bateriaproveedores')
        ->select('area')
        ->where('tipoarea', $tipo)
        ->where('sucursal', $sucursal)
        ->where('estado', 'ACTIVO')
        ->where('asociado', 'CLIENTES ITA')
        ->distinct()
        ->get();
});

Route::get('/acciones', function(Request $request) {
    $area = $request->query('area');
    $sucursal = $request->query('sucursal');

    return DB::table('bateriaproveedores')
        ->select('accion')
        ->where('area', $area)
        ->where('sucursal', $sucursal)
        ->where('estado', 'ACTIVO')
        ->where('asociado', 'CLIENTES ITA')
        ->distinct()
        ->get();
});

Route::get('/proveedores', function(Request $request) {
    $area = $request->query('area');
    $accion = $request->query('accion');
    $sucursal = $request->query('sucursal');

    $query = DB::table('bateriaproveedores')
        ->select('proveedor', 'horarioinicial', 'horariofinal', 'tiempoatencion', 'precio')
        ->where('area', $area)
        ->where('sucursal', $sucursal)
        ->where('estado', 'ACTIVO')
        ->where('asociado', 'CLIENTES ITA');

    if ($accion) $query->where('accion', $accion);

    return $query->get();
});

Route::get('/clientes/{userId}', function($userId) {

    $cliente = DB::table('clientes')
        ->where('id', function($query) use ($userId) {
            $query->select('clienteid')
                  ->from('users')
                  ->where('id', $userId)
                  ->limit(1);
        })
        ->first();

    if (!$cliente) {
        return response()->json(['message' => 'Cliente no encontrado'], 404);
    }

    return response()->json($cliente);
});

Route::get('/documentos/{userId}', function($userId) {
    // Buscar clienteid del usuario
    $clienteId = DB::table('users')
        ->where('id', $userId)
        ->value('clienteid');

    if (!$clienteId) {
        return response()->json(['message' => 'Cliente no encontrado'], 404);
    }

    // Obtener documentos relacionados
    $documentos = DB::table('documentacionsubclientes')
        ->where('clienteitaid', $clienteId)
        ->get();

    return response()->json($documentos);
});

Route::get('/tramites/{userId}', function($userId) {

    // 1️⃣ Obtener clienteid del usuario
    $clienteId = DB::table('users')
        ->where('id', $userId)
        ->value('clienteid');

    if (!$clienteId) {
        return response()->json(['message' => 'Cliente no encontrado'], 404);
    }

    // 2️⃣ Obtener trámites del cliente
    $tramites = DB::table('tramitessubclientes')
        ->where('clienteitaid', $clienteId)
        ->whereNull('deleted_at')
        ->get();

    if ($tramites->isEmpty()) {
        return response()->json(['message' => 'No hay trámites'], 200);
    }

    // 3️⃣ Obtener IDs de esos trámites
    $ids = $tramites->pluck('id')->toArray();

    // 4️⃣ Obtener procedimientos A PARTIR de esos idtramite
    $procedimientos = DB::table('procedimientotramites')
        ->whereIn('idtramite', $ids)
        ->whereNull('deleted_at')
        ->get();

    return response()->json([
        'tramites' => $tramites,
        'procedimientos' => $procedimientos,
    ]);
});

Route::get('/tramite/{idTramite}', function($idTramite) {

    // Obtener trámite
    $tramite = DB::table('tramitessubclientes')
        ->where('id', $idTramite)
        ->whereNull('deleted_at')
        ->first();

    if (!$tramite) {
        return response()->json(['message' => 'Trámite no encontrado'], 404);
    }

    // Obtener procedimientos del trámite
    $procedimientos = DB::table('procedimientotramites')
        ->where('idtramite', $idTramite)
        ->whereNull('deleted_at')
        ->where('tipo', '!=', 'SEGUIMIENTO')
        ->get();

    return response()->json([
        'tramite' => $tramite,
        'procedimientos' => $procedimientos,
    ]);
});

Route::get('/notificaciones/{userId}', function ($userId) {

    $user = User::findOrFail($userId);

    return response()->json([
        'no_leidas' => $user->notifications()
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get(),

        'leidas' => $user->notifications()
            ->whereNotNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get(),
    ]);
});
Route::get('/notificaciones/{userId}', [NotificacionController::class, 'listar']);
Route::post('/notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida']);


// ... otras rutas

// Agrupa las rutas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Aquí pueden ir otras rutas de tu API que necesiten login...

    // AÑADE ESTA RUTA PARA ACTUALIZAR EL TOKEN
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);
});
    