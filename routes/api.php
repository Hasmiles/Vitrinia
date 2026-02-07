<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SellerController;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'E-posta adresi zaten doğrulanmış.'], 400);
    }

    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Doğrulama bağlantısı e-posta adresinize tekrar gönderildi!']);
})->middleware(['auth:sanctum', 'throttle:6,1']);

// Route::get('/bildirim-test', function () {
//     $user = User::find(32);
//     $token = trim($user->fcm_token);

//     dump('Gönderilen Token: ' . $token);

//     $response = Http::post('https://exp.host/--/api/v2/push/send', [
//         'to' => $token,
//         'title' => 'Son Test 🚀',
//         'body' => 'Parantez sorunu çözüldü, bu mesaj gelmeli.',
//         'data' => ['test' => true],
//         'sound' => 'default',  // Ses çalsın
//     ]);

//     return $response->json();
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
    Route::resource('sellers/me', SellerController::class);
    Route::post('sellers/update_push_token', [SellerController::class, 'updatePushToken']);
    Route::delete('sellers/me', [SellerController::class, 'destroy']);
    Route::resource('products', ProductController::class);
    Route::delete('/products', [ProductController::class, 'destroy']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'changeSituationForOrder']);

    Route::get('/sellers/report', [SellerController::class, 'reportForSeller']);
    Route::get('/sellers/dashboard', [SellerController::class, 'psql_dashboard']);
    Route::get('/orders/{id}/label', [OrderController::class, 'createLabel']);
    Route::resource('orders', OrderController::class)->except(['update']);
});
Route::put('orders/{short_code}', [OrderController::class, 'update']);
Route::get('/orders/public/{kisa_kod}', [OrderController::class, 'getOrderDetailsForCustomer']);
Route::get('options', [GeneralController::class, 'options']);
Route::get('status', [GeneralController::class, 'statuses']);
