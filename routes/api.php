<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\NotificacionController;
use App\Http\Controllers\Api\AuthController; // Asegúrate de apuntar al controlador correcto
use Illuminate\Support\Facades\Auth;
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
        ->where('proveedor', '!=', 'PROVEEDOR AJENO')
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
        /* ->select('proveedor', 'horarioinicial', 'horariofinal', 'tiempoatencion', 'precio') */
        ->select(DB::raw("
            CASE 
                WHEN TRIM(UPPER(proveedor)) = 'MARIA ANGELA LOZANO FLORES'
                    THEN 'PROVEEDOR MEDICO'
                ELSE proveedor
            END as proveedor,
            horarioinicial,
            horariofinal,
            tiempoatencion,
            precio
        "))
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
    ->whereNotIn('accion', ['DIAGNÓSTICO MÉDICO', 'MEDICINA LABORAL', 'HISTORIA MÉDICA'])
    ->orderBy('accion', 'asc') // o 'desc'
    ->whereNull('deleted_at')
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

Route::middleware('auth:sanctum')->group(function () {
    // ... tus otras rutas privadas

    /* Route::get('/proveedor-detalle/{accionId}', function($accionId) {
        // 1. Obtener el usuario y su sucursal (como ya lo tienes)
        $user = Auth::user();
        if (!$user || !$user->clienteid) {
            return response()->json(['message' => 'Usuario no válido'], 401);
        }
        $cliente = DB::table('clientes')->where('id', $user->clienteid)->first();
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
        $sucursalUsuario = $cliente->sucursal;

        // 2. Obtener la información del proveedor
        $proveedorInfo = DB::table('bateriaproveedores')
            ->where('id', $accionId)
            ->select('proveedor', 'precio', 'proveedorid')
            ->first();
        if (!$proveedorInfo) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        // 3. Buscamos los horarios ACTIVOS para ese proveedor y sucursal
        $horarios = DB::table('atmedhorarios')
            ->where('proveedorid', $proveedorInfo->proveedorid)
            ->where('sucursal', $sucursalUsuario)
            ->where('estado', 'ACTIVO')
            ->select('dia', 'horainicio', 'horafin', 'duracioncita', 'tipo')
            ->orderBy('dia')
            ->get();
            
        // ▼▼▼ NUEVA LÓGICA AÑADIDA ▼▼▼
        // 4. Buscamos TODOS los bloqueos para ese proveedor y sucursal
        $bloqueos = DB::table('atmedbloqueos')
            ->where('proveedorid', $proveedorInfo->proveedorid)
            ->where('sucursal', $sucursalUsuario)
            ->whereNull('deleted_at')
            ->select('fecha', 'horainicio', 'horafin')
            ->get();
        // ▲▲▲ FIN DE LA NUEVA LÓGICA ▲▲▲


        $reservas = DB::table('programacionsubclientes')
            ->where('proveedornombre', $proveedorInfo->proveedor) // Filtramos por el nombre del proveedor
            ->where('fechaasignada', '>=', now()->toDateString()) // Desde hoy
            ->where('fechaasignada', '<=', now()->addDays(7)->toDateString()) // Hasta 7 días en el futuro
            ->whereNull('deleted_at') // Que no estén canceladas
            ->select('fechaasignada', 'horadesde') // Solo necesitamos estos dos datos
            ->get();
        // 5. Combinamos y devolvemos la respuesta
        return response()->json([
            'proveedor' => $proveedorInfo->proveedor,
            'precio' => $proveedorInfo->precio,
            'duracioncita' => $horarios->isNotEmpty() ? $horarios->first()->duracioncita : null, 
            'horarios' => $horarios,
            'bloqueos' => $bloqueos,
            'reservas' => $reservas // <-- Devolvemos la lista de bloqueos
        ]);
    }); */
    /*     Route::get('/proveedor-detalle/{accionId}', function($accionId) {
        // 1. Obtener usuario y sucursal
        $user = Auth::user();
        if (!$user || !$user->clienteid) {
            return response()->json(['message' => 'Usuario no válido'], 401);
        }
        $cliente = DB::table('clientes')->where('id', $user->clienteid)->first();
        $sucursalUsuario = $cliente->sucursal;

        // 2. Obtener la información del proveedor específico
        $proveedorInfo = DB::table('bateriaproveedores')
            ->where('id', $accionId)
            ->select('proveedor', 'precio', 'proveedorid', 'area')
            ->first();

        if (!$proveedorInfo) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        // 3. NUEVA LÓGICA: Obtener tiempoatencion por ÁREA desde bateriaproveedores
        $tiempoAtencion = DB::table('bateriaproveedores')
            ->where('area', $proveedorInfo->area)
            ->whereNotNull('tiempoatencion')
            ->where('tiempoatencion', '>', 0)
            ->value('tiempoatencion'); // Solo toma uno como solicitaste

        // 4. Buscamos horarios ACTIVOS
        $horarios = DB::table('atmedhorarios')
            ->where('proveedorid', $proveedorInfo->proveedorid)
            ->where('sucursal', $sucursalUsuario)
            ->where('estado', 'ACTIVO')
            ->select('dia', 'horainicio', 'horafin', 'tipo')
            ->orderBy('dia')
            ->get();
            
        // 5. Bloqueos y Reservas
        $bloqueos = DB::table('atmedbloqueos')
            ->where('proveedorid', $proveedorInfo->proveedorid)
            ->where('sucursal', $sucursalUsuario)
            ->select('fecha', 'horainicio', 'horafin')
            ->get();

        $reservas = DB::table('programacionsubclientes')
            ->where('proveedornombre', $proveedorInfo->proveedor)
            ->where('fechaasignada', '>=', now()->toDateString())
            ->where('fechaasignada', '<=', now()->addDays(7)->toDateString())
            ->whereNull('deleted_at')
            ->select('fechaasignada', 'horadesde')
            ->get();

        return response()->json([
            'proveedor' => $proveedorInfo->proveedor,
            'precio' => $proveedorInfo->precio,
            'duracioncita' => $tiempoAtencion, // <--- SE ENVÍA EL TIEMPO POR ÁREA
            'horarios' => $horarios,
            'bloqueos' => $bloqueos,
            'reservas' => $reservas
        ]);
    }); */
    Route::get('/proveedor-detalle/{accionId}', function($accionId) {
    $user = Auth::user();
    if (!$user || !$user->clienteid) {
        return response()->json(['message' => 'Usuario no válido'], 401);
    }
    $cliente = DB::table('clientes')->where('id', $user->clienteid)->first();
    $sucursalUsuario = $cliente->sucursal;

    $proveedorInfo = DB::table('bateriaproveedores')
        ->where('id', $accionId)
        ->select('proveedor', 'precio', 'proveedorid', 'area')
        ->first();

    if (!$proveedorInfo) {
        return response()->json(['message' => 'Proveedor no encontrado'], 404);
    }

    // 1. Tiempo de atención por área
    $tiempoAtencion = DB::table('bateriaproveedores')
        ->where('area', $proveedorInfo->area)
        ->whereNotNull('tiempoatencion')
        ->where('tiempoatencion', '>', 0)
        ->value('tiempoatencion');

    // 2. Horarios FIJOS (Semanales)
    $horarios = DB::table('atmedhorarios')
        ->where('proveedorid', $proveedorInfo->proveedorid)
        ->where('sucursal', $sucursalUsuario)
        ->where('estado', 'ACTIVO')
        ->select('dia', 'horainicio', 'horafin', 'tipo')
        ->orderBy('dia')
        ->get();

    // 3. NUEVA LÓGICA: Horarios DIARIOS (Específicos por fecha)
    $diarios = DB::table('atmeddiario')
        ->where('proveedorid', $proveedorInfo->proveedorid)
        ->where('sucursal', $sucursalUsuario)
        ->where('fecha', '>=', now()->toDateString())
        ->where('fecha', '<=', now()->addDays(14)->toDateString()) // Buscamos un rango amplio
        ->select('fecha', 'horainicio', 'horafin')
        ->get();
        
    // 4. Bloqueos y Reservas
    $bloqueos = DB::table('atmedbloqueos')
        ->where('proveedorid', $proveedorInfo->proveedorid)
        ->where('sucursal', $sucursalUsuario)
        ->select('fecha', 'horainicio', 'horafin')
        ->get();

    $reservas = DB::table('programacionsubclientes')
        ->where('proveedornombre', $proveedorInfo->proveedor)
        ->where('fechaasignada', '>=', now()->toDateString())
        ->whereNull('deleted_at')
        ->select('fechaasignada', 'horadesde')
        ->get();

    return response()->json([
        'proveedor' => $proveedorInfo->proveedor,
        'precio' => $proveedorInfo->precio,
        'duracioncita' => $tiempoAtencion,
        'horarios' => $horarios,
        'diarios' => $diarios, // <--- ENVIAMOS LOS DIARIOS
        'bloqueos' => $bloqueos,
        'reservas' => $reservas
    ]);
});
});
// Agrupa las rutas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {

    // AÑADE ESTA RUTA PARA ACTUALIZAR EL TOKEN
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);

    /* Route::post('/programar-cita', function(Request $request) {

        // 1. Validar datos que llegan (sin cambios)
        $validatedData = $request->validate([
            'bateria_id' => 'required|integer|exists:bateriasubclientes,id',
            'fecha' => 'required|date',
            'horadesde' => 'required',
            'horahasta' => 'required',
        ]);

        // 2. Obtener usuario y cliente (sin cambios)
        $user = Auth::user();
        if (!$user || !$user->clienteid) {
            return response()->json(['message' => 'Usuario no válido'], 401);
        }
        $cliente = DB::table('clientes')->where('id', $user->clienteid)->first();
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        // 3. Buscar el registro original de la batería
        $bateriaOriginal = DB::table('bateriasubclientes')->where('id', $validatedData['bateria_id'])->first();
        if (!$bateriaOriginal) {
            return response()->json(['message' => 'Batería original no encontrada'], 404);
        }

        // ▼▼▼ ¡NUEVA LÓGICA DE VERIFICACIÓN! ▼▼▼
        // 4. Comprobar si el horario ya fue tomado por otra persona
        $isSlotTaken = DB::table('programacionsubclientes')
            ->where('proveedornombre', $bateriaOriginal->proveedorasignado)
            ->where('fechaasignada', $validatedData['fecha'])
            ->where('horadesde', $validatedData['horadesde'])
            ->whereNull('deleted_at')
            ->exists();

        if ($isSlotTaken) {
            // Si el horario ya existe, devolvemos un error de conflicto (409)
            return response()->json(['message' => 'Este horario ya no está disponible. Por favor, seleccione otro.'], 409);
        }
        // ▲▲▲ FIN DE LA NUEVA LÓGICA ▲▲▲

        // 5. Si todo está bien, insertar el registro
        try {
            DB::table('programacionsubclientes')->insert([
                'bateriaid' => $bateriaOriginal->id,
                'clienteitaid' => $cliente->id,
                // ... y todos los demás campos que ya tienes ...
                'clienteitanombre'  => $cliente->nombrecompleto,
                'accionnombre'      => $bateriaOriginal->accionnombre,
                'proveedornombre'   => $bateriaOriginal->proveedorasignado,
                'fechabateria'      => $bateriaOriginal->fechabateria,
                'servicio'          => $bateriaOriginal->servicio,
                'precio'            => $bateriaOriginal->precio,
                'preciocompra'      => $bateriaOriginal->preciocompra,
                'areanombre'        => $bateriaOriginal->areanombre,
                'pagoservicio'      => $bateriaOriginal->pagoservicio,
                'medicoderivante'   => $bateriaOriginal->medicoderivante,
                'idsubproc'         => $bateriaOriginal->idsubproc,
                'clienteid'         => $cliente->id,
                'clientenombre'     => $cliente->nombrecompleto,
                'tipocliente'       => 'ITA',
                'fechaasignada'     => $validatedData['fecha'],
                'horadesde'         => $validatedData['horadesde'],
                'horahasta'         => $validatedData['horahasta'],
                'usuarioid'         => $user->id,
                'usuarioregistro'   => $user->name,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al guardar en la base de datos', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Programación creada con éxito'], 201);
    }); */
    Route::post('/programar-cita', function(Request $request) {
    $validatedData = $request->validate([
        'bateria_id' => 'required|integer|exists:bateriasubclientes,id',
        'fecha' => 'required|date',
        'horadesde' => 'required',
        'horahasta' => 'required',
    ]);

    $user = Auth::user();
    $cliente = DB::table('clientes')->where('id', $user->clienteid)->first();
    $ref = DB::table('bateriasubclientes')->where('id', $validatedData['bateria_id'])->first();
    
    $items = DB::table('bateriasubclientes')
        ->where('clienteitaid', $cliente->id)
        ->where('areanombre', $ref->areanombre)
        ->where('accionnombre', '!=', 'INFORME FINAL')
        ->where('proveedorasignado', '!=', 'PROVEEDOR AJENO')
        ->get();

    try {
        foreach ($items as $item) {
            $existe = DB::table('programacionsubclientes')->where('bateriaid', $item->id)->whereNull('deleted_at')->exists();
            if (!$existe) {
                DB::table('programacionsubclientes')->insert([
                    'bateriaid' => $item->id, 'clienteitaid' => $cliente->id, 'clienteitanombre' => $cliente->nombrecompleto,
                    'usuarioid' => $user->id, 'usuarioregistro' => $user->name, 'fechaasignada' => $validatedData['fecha'],
                    'horadesde' => $validatedData['horadesde'], 'horahasta' => $validatedData['horahasta'],
                    'areanombre' => $item->areanombre, 'accionnombre' => $item->accionnombre,
                    'proveedornombre' => $item->proveedorasignado, 'fechabateria' => $item->fechabateria,
                    'servicio' => $item->servicio, 'precio' => $item->precio, 'preciocompra' => $item->preciocompra,
                    'pagoservicio' => $item->pagoservicio, 'medicoderivante' => $item->medicoderivante,
                    'idsubproc' => $item->idsubproc, 'clienteid' => $cliente->id, 'clientenombre' => $cliente->nombrecompleto,
                    'tipocliente' => 'ITA', 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al guardar', 'error' => $e->getMessage()], 500);
    }
    return response()->json(['message' => 'Programación creada con éxito'], 201);
});

Route::post('/confirmar-asistencia', function(Request $request) {
    $validated = $request->validate([
        'areanombre' => 'required|string',
        'decision'=> 'required|string|in:SI,NO',
        'usuario_id' => 'required'
    ]);

    $clienteId = DB::table('users')->where('id', $validated['usuario_id'])->value('clienteid');

    if (!$clienteId) {
        return response()->json(['message' => 'Cliente no encontrado'], 404);
    }

    try {
        // Actualizamos todos los registros de esa área para ese cliente
        DB::table('programacionsubclientes')
            ->where('clienteitaid', $clienteId)
            ->where('areanombre', $validated['areanombre'])
            ->whereNull('deleted_at')
            ->update([
                'confasistencia' => $validated['decision'], // Usando el nombre de columna que indicaste
                'updated_at'     => now(),
            ]);

        return response()->json(['message' => 'Asistencia confirmada correctamente']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al actualizar', 'error' => $e->getMessage()], 500);
    }
});

});
    

/* Route::get('/baterias/{userId}', function($userId) {
    // 1. Obtener clienteId (sin cambios)
    $clienteId = DB::table('users')
        ->where('id', $userId)
        ->value('clienteid');

    if (!$clienteId) {
        return response()->json(['message' => 'Cliente no encontrado'], 404);
    }

    // ▼▼▼ LÓGICA DE CONSULTA MODIFICADA ▼▼▼
    $documentos = DB::table('bateriasubclientes as bsc')
        // Unimos con 'programacionsubclientes' para saber si ya fue programado y obtener su ID
        ->leftJoin('programacionsubclientes as psc', function ($join) {
            $join->on('bsc.id', '=', 'psc.bateriaid')
                 ->whereNull('psc.deleted_at'); // Ignorar si la programación fue cancelada
        })
        // Unimos con 'estadoprogramacionsubclientes' para saber si ya tiene un estado
        ->leftJoin('estadoprogramacionsubclientes as epsc', function ($join) {
            $join->on('psc.id', '=', 'epsc.programacionid')
                 ->whereNull('epsc.deleted_at'); // Ignorar si el estado fue eliminado
        })
        ->where('bsc.clienteitaid', $clienteId)
        ->where('bsc.accionnombre', '!=', 'INFORME FINAL')
        ->where('bsc.proveedorasignado', '!=', 'PROVEEDOR AJENO')
        
        // LA CONDICIÓN CLAVE: Solo mostrar si NO TIENE un estado registrado.
        // Si 'epsc.id' es null, significa que no encontró una coincidencia en la tabla de estados.
        ->whereNull('epsc.id') 
        
        ->select(
            'bsc.*', // Datos de la batería original
            'psc.fechaasignada', // Datos de la programación (serán null si aún no está programado)
            'psc.horadesde',
            'psc.horahasta'
        )
        ->get();
    // ▲▲▲ FIN DE LA LÓGICA MODIFICADA ▲▲▲

    return response()->json($documentos);
}); */
/* Route::get('/baterias/{userId}', function($userId) {
    $clienteId = DB::table('users')->where('id', $userId)->value('clienteid');

    if (!$clienteId) {
        return response()->json(['message' => 'Cliente no encontrado'], 404);
    }

    $documentos = DB::table('bateriasubclientes as bsc')
        ->leftJoin('programacionsubclientes as psc', function ($join) {
            $join->on('bsc.id', '=', 'psc.bateriaid')
                 ->whereNull('psc.deleted_at');
        })
        ->leftJoin('estadoprogramacionsubclientes as epsc', function ($join) {
            $join->on('psc.id', '=', 'epsc.programacionid')
                 ->whereNull('epsc.deleted_at');
        })
        ->where('bsc.clienteitaid', $clienteId)
        ->where('bsc.accionnombre', '!=', 'INFORME FINAL')
        ->where('bsc.proveedorasignado', '!=', 'PROVEEDOR AJENO')
        ->whereNull('epsc.id') 
        ->select(
            'bsc.areanombre',
            // Concatenamos las acciones
            DB::raw('GROUP_CONCAT(bsc.accionnombre SEPARATOR ", ") as acciones_detalle'),
            // Sumamos los precios
            DB::raw('SUM(bsc.precio) as precio_total'), 
            DB::raw('MIN(bsc.id) as id'), 
            DB::raw('MIN(bsc.accionid) as accionid'),
            DB::raw('MAX(psc.fechaasignada) as fechaasignada'),
            DB::raw('MAX(psc.horadesde) as horadesde'),
            DB::raw('MAX(psc.horahasta) as horahasta')
        )
        ->groupBy('bsc.areanombre')
        ->get();

    return response()->json($documentos);
}); */

Route::get('/baterias/{userId}', function($userId) {
    // 1. Obtener clienteId
    $clienteId = DB::table('users')->where('id', $userId)->value('clienteid');

    if (!$clienteId) {
        return response()->json(['message' => 'Cliente no encontrado'], 404);
    }

    // ▼▼▼ NUEVA LÓGICA DE BLOQUEO ▼▼▼
    // Contamos cuántas veces el cliente ha cancelado (usuarioeliminacion = 'CLIENTE')
    // Usamos onlyTrashed() o comprobamos donde deleted_at no sea null si no usas SoftDeletes de Eloquent
    $cancelaciones = DB::table('programacionsubclientes')
        ->where('clienteitaid', $clienteId)
        ->where('usuarioeliminacion', 'CLIENTE')
        /* ->whereNotNull('deleted_at') */
        ->count();

    $isBlocked = ($cancelaciones >= 2);
    // ▲▲▲ FIN LÓGICA DE BLOQUEO ▲▲▲

    // 2. Consulta de documentos (agrupada por área como ya la tienes)
    $documentos = DB::table('bateriasubclientes as bsc')
        ->leftJoin('programacionsubclientes as psc', function ($join) {
            $join->on('bsc.id', '=', 'psc.bateriaid')->whereNull('psc.deleted_at');
        })
        ->leftJoin('estadoprogramacionsubclientes as epsc', function ($join) {
            $join->on('psc.id', '=', 'epsc.programacionid')->whereNull('epsc.deleted_at');
        })
        ->where('bsc.clienteitaid', $clienteId)
        ->where('bsc.accionnombre', '!=', 'INFORME FINAL')
        ->where('bsc.proveedorasignado', '!=', 'PROVEEDOR AJENO')
        ->whereNull('epsc.id') 
        ->select(
            'bsc.areanombre',
            DB::raw('GROUP_CONCAT(bsc.accionnombre SEPARATOR ", ") as acciones_detalle'),
            DB::raw('SUM(bsc.precio) as precio_total'), 
            DB::raw('MIN(bsc.id) as id'), 
            DB::raw('MIN(bsc.accionid) as accionid'),
            DB::raw('MAX(psc.fechaasignada) as fechaasignada'),
            DB::raw('MAX(psc.horadesde) as horadesde'),
            DB::raw('MAX(psc.horahasta) as horahasta'),
            DB::raw('MAX(psc.confasistencia) as confasistencia')
        )
        ->groupBy('bsc.areanombre')
        ->get();

    // Devolvemos un objeto con la lista y el estado de bloqueo
    return response()->json([
        'baterias' => $documentos,
        'is_blocked' => $isBlocked
    ]);
});

Route::get('/ausencias', function() {
    // 1. Obtener la fecha actual para no mostrar ausencias pasadas
    $hoy = now()->toDateString();

    // 2. Consultar la tabla 'atmedbloqueos'
    $ausencias = DB::table('atmedbloqueos')
        // 3. Filtrar para obtener solo fechas desde hoy en adelante
        ->where('fecha', '>=', $hoy)
        ->whereNull('deleted_at')
        // 4. Seleccionar las columnas que la app necesita
        ->select('proveedornombre', 'motivo', 'fecha', 'horainicio', 'horafin')
        // 5. Ordenar por proveedor y luego por fecha para que la agrupación en la app sea más fácil
        ->orderBy('proveedornombre')
        ->orderBy('fecha')
        ->get();

    // 6. Devolver los resultados como JSON
    return response()->json($ausencias);
});

