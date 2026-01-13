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
        // 1. Token boşsa işlem yapma
        if (empty($token)) {
            Log::warning('FCMService: Token boş geldi.');
            return false;
        }

        // 2. Token formatını temizle (Boşluk vs. varsa siler)
        $token = trim($token);

        // 3. Expo Token Kontrolü (İsteğe bağlı ama güvenli)
        // Eğer token ExponentPushToken ile başlamıyorsa Expo bunu kabul etmez.
        if (!str_starts_with($token, 'ExponentPushToken')) {
            Log::error('FCMService: Geçersiz Expo Token formatı: ' . $token);
            return false;
        }

        try {
            // 4. İsteği Expo Sunucularına Atıyoruz (Firebase'e değil!)
            $response = Http::post('https://exp.host/--/api/v2/push/send', [
                'to'    => $token,
                'title' => $title,
                'body'  => $body,
                'data'  => $data,
                'sound' => 'default', // Bildirim sesi çıksın
                'badge' => 1,         // Uygulama ikonunda 1 yazsın
            ]);

            // 5. Sonucu kontrol et
            if ($response->successful()) {
                $result = $response->json();
                
                // Expo sunucusu "200 OK" dönse bile içindeki data'da 'error' olabilir
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