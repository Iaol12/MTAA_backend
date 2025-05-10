<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushNotificationService
{
    public function sendPushNotification($expoToken, $title, $body)
    {
        try {
            // For development environments - disable SSL verification
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post('https://exp.host/--/api/v2/push/send', [
                    'to' => $expoToken,
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'priority' => 'high',
                ]);

            // Log response for debugging
            Log::debug('Expo push notification response: ' . $response->body());
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error sending push notification: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
