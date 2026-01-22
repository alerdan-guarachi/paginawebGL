<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificacionController extends Controller
{
    public function listar($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $typesPermitidos = [
            'App\\Notifications\\ClienteReferenciadoNotification',
            'App\Notifications\AvanceTramiteNotification'
        ];

        // 🔔 NO LEÍDAS
        $noLeidas = $user->unreadNotifications()
            ->whereIn('type', $typesPermitidos)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'data' => $n->data,
                    'read_at' => $n->read_at,
                    'created_at' => $n->created_at,
                ];
            });

        // 📘 LEÍDAS
        $leidas = $user->readNotifications()
            ->whereIn('type', $typesPermitidos)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'data' => $n->data,
                    'read_at' => $n->read_at,
                    'created_at' => $n->created_at,
                ];
            });

        return response()->json([
            'no_leidas' => $noLeidas,
            'leidas' => $leidas,
        ]);
    }

    // Marcar notificación como leída
    public function marcarLeida($id)
    {
        $notification = DB::table('notifications')->where('id', $id)->first();

        if (!$notification) {
            return response()->json(['message' => 'Notificación no encontrada'], 404);
        }

        DB::table('notifications')
            ->where('id', $id)
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Notificación marcada como leída']);
    }
}
