<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FCMService
{
    /**
     * Tek bir kullanıcıya Expo üzerinden bildirim gönderir.
     *
     * @param string $token  Kullanıcının ExponentPushToken'ı
     * @param string $title  Bildirim Başlığı
     * @param string $body   Bildirim İçeriği
     * @param array  $data   (Opsiyonel) Ek veriler
     */
    public function sendToUser($token, $title, $body, $data = [])
    {
        if (empty($token)) {
            Log::warning('FCMService: Token boş geldi.');
            return false;
        }
        $token = trim($token);

        try {
            $response = Http::post('https://exp.host/--/api/v2/push/send', [
                'to'    => $token,
                'title' => $title,
                'body'  => $body,
                'data'  => $data,
                'sound' => 'default', 
                'badge' => 1,        
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['data']['status']) && $result['data']['status'] === 'error') {
                    Log::error('Expo API Hatası: ' . json_encode($result['data']));
                    return false;
                }

                Log::info('Expo Bildirimi Başarılı: ' . $token);
                return true;
            } else {
                Log::error('Expo Bağlantı Hatası: ' . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            Log::error('FCMService Exception: ' . $e->getMessage());
            return false;
        }
    }
}