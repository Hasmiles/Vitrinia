<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Link geçersiz veya bozulmuş!'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'E-posta zaten doğrulanmış.']);
    }

  
    $user->forceFill([
        'email_verified_at' => now(), // Şu anki zamanı basar
        'is_verified' => true,      
    ])->save(); 
    event(new Verified($user));

    return response()->json(['message' => 'Hesabınız başarıyla doğrulandı!']);
    // return redirect('http://localhost:3000/basarili?verified=1');

})->middleware(['signed'])->name('verification.verify');

Route::get('/kurulum-yap', function () {
    try {
        // 1. Tabloları oluştur/güncelle (Migrate)
        Artisan::call('migrate', ['--force' => true]);
        $cikti = '<h3>Migration Çıktısı:</h3><pre>' . Artisan::output() . '</pre>';

        // 2. Seed işlemini çalıştır (Veri Ekleme)
        // Not: --force production ortamında onay sormaması için şarttır.
        Artisan::call('db:seed', ['--force' => true]);
        $cikti .= '<h3>Seed Çıktısı:</h3><pre>' . Artisan::output() . '</pre>';
        
        return $cikti;
    } catch (\Exception $e) {
        return '<h1 style="color:red">Hata Oluştu!</h1><p>' . $e->getMessage() . '</p>';
    }
});

Route::get('/manuel-duzeltme', function () {
    // ÖRNEK: ID'si 1 olan kullanıcıyı bul ve aktif et
    $user = User::find(1); 
    if ($user) {
        $user->email_verified_at = now(); // Veya $user->status = 1;
        $user->save();
        return "Kral, kullanıcı (ID: 1) başarıyla aktif edildi! ✅";
    }
    
    return "Böyle bir kayıt bulunamadı kral. ID'yi kontrol et. ❌";
});