<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExpoPushNotificationService
{
    public function sendPushNotification($expoToken, $title, $body)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://exp.host/--/api/v2/push/send', [
            'to' => $expoToken,
            'title' => $title,
            'body' => $body,
            'priority' => 'high',
        ]);

        return $response->json();
    }
}
