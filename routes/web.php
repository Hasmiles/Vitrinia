<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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

