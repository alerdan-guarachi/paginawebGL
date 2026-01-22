<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirebaseService
{
    public static function sendNotification($deviceToken, $title, $body)
    {
        $accessToken = self::getAccessToken();

        return Http::withToken($accessToken)
            ->post(
                'https://fcm.googleapis.com/v1/projects/' . config('services.firebase.project_id') . '/messages:send',
                [
                    'message' => [
                        'token' => $deviceToken,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                    ],
                ]
            );
    }

    protected static function getAccessToken()
    {
        $jsonKey = json_decode(
            file_get_contents(storage_path('app/firebase_credentials.json')),
            true
        );

        $now = time();

        $jwtHeader = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $jwtClaimSet = base64_encode(json_encode([
            'iss'   => $jsonKey['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $signatureInput = $jwtHeader . '.' . $jwtClaimSet;

        openssl_sign(
            $signatureInput,
            $signature,
            $jsonKey['private_key'],
            'SHA256'
        );

        $jwt = $signatureInput . '.' . base64_encode($signature);

        $response = Http::asForm()->post(
            'https://oauth2.googleapis.com/token',
            [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]
        );

        return $response->json('access_token');
    }
}
