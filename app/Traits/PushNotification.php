<?php


namespace App\Traits;

use Google\Auth\ApplicationDefaultCredentials;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Log;
trait PushNotification
{


    public function sendNotification($token, $title, $body, $data = [])
    {
        $fcmurl = "https://fcm.googleapis.com/v1/projects/gleegather-lms/messages:send";

        $notification = [
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => $data,
            'token' => $token,

        ];

        try {
            Log::info('Access Token: ' . $this->getAccessToken());

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'content-Type' => 'application/json',
            ])->post($fcmurl, ['message' => $notification]);
            Log::info('FCM Response: ', $response->json());

            return response()->json([
                'success' => true,
                'response' => $response->json(),
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    private function getAccessToken()
    {
        $keyPath = config('services.firebase.key_path');
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $keyPath);

        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = ApplicationDefaultCredentials::getCredentials($scopes);
        $token = $credentials->fetchAuthToken();

        return $token['access_token'] ?? null;
    }
}




