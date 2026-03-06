<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Client as GoogleClient; // Importante: requiere google/apiclient

class NotificarCitasProximas extends Command
{
    protected $signature = 'notificar:citas';
    protected $description = 'Envía recordatorios de citas 1 hora antes vía FCM v1';

    public function handle()
    {
        // 1. Ajustar hora local (Bolivia)
        $ahora = now('America/La_Paz');
        $hoy = $ahora->toDateString();
        $unaHoraDespues = $ahora->addHour()->format('H:i');

        $this->info("Buscando citas para hoy ($hoy) a las $unaHoraDespues...");

        $citas = DB::table('programacionsubclientes as psc')
            ->join('users as u', 'psc.clienteitaid', '=', 'u.clienteid')
            ->where('psc.fechaasignada', $hoy)
            ->where('psc.horadesde', 'LIKE', "$unaHoraDespues%")
            ->whereNotNull('u.fcm_token')
            ->whereNull('psc.deleted_at')
            ->select('u.fcm_token', 'psc.accionnombre', 'psc.horadesde', 'u.name')
            ->get();

        if ($citas->isEmpty()) {
            $this->info("Sin citas para este minuto.");
            return;
        }

        // 2. Obtener el Token de Acceso de Google (OAuth2)
        $accessToken = $this->getGoogleAccessToken();

        if (!$accessToken) {
            $this->error("No se pudo obtener el token de acceso de Google.");
            return;
        }

        foreach ($citas as $cita) {
            $this->info("Enviando a: {$cita->name}");
            
            $this->enviarNotificacionFCMV1(
                $accessToken,
                $cita->fcm_token,
                "Recordatorio de Cita Médica",
                "Hola {$cita->name}, porfavor confirma tu cita médica para {$cita->accionnombre} a las " . substr($cita->horadesde, 0, 5)
            );
        }
    }

    // ▼▼▼ FUNCIÓN PARA GENERAR EL TOKEN OAUTH2 ▼▼▼
    private function getGoogleAccessToken()
{
    $path = storage_path('app/firebase_credentials.json');
    
    if (!file_exists($path)) {
        $this->error("ERROR: El archivo no existe en: $path");return null;
    }

    try {
        $client = new \Google\Client(); // Asegúrate de la barra invertida \
        $client->setAuthConfig($path);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        
        $token = $client->fetchAccessTokenWithAssertion();
        
        if (isset($token['error'])) {
            $this->error("Error de Google: " . $token['error_description']);
            return null;
        }

        return $token['access_token'] ?? null;

    } catch (\Exception $e) {
        $this->error("Excepción al obtener token: " . $e->getMessage());
        return null;
    }
}

    // ▼▼▼ FUNCIÓN PARA ENVIAR VÍA API V1 ▼▼▼
    private function enviarNotificacionFCMV1($accessToken, $fcmToken, $titulo, $mensaje)
    {
        // Reemplaza 'TU_PROYECTO_ID' con el project_id que está dentro de tu JSON
        $projectId = 'appgoodlife-31425'; 
        $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/json',
        ])->post($url, [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $titulo,
                    'body'  => $mensaje,
                ],
                'data' => [
                    'type' => 'recordatorio',
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                    ],
                ],
            ],
        ]);

        if ($response->successful()) {
            Log::info("Notificación V1 enviada: $fcmToken");
        } else {
            Log::error("Error FCM V1: " . $response->body());
        }
    }
}