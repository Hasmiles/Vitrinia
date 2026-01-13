<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        // DB::transaction başlatıyoruz. İçerideki işlemlerden biri patlarsa hepsi iptal olur.
        try {
            $user = User::create([
                'name' => $request->owner_name ?? '',
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ])->makeHidden('name');

            event(new Registered($user));  // mail gönderme

            Seller::create([
                'user_id' => $user->id,
                'shop_name' => $request->shop_name ?? '',
                'logo' => '',
                'phone' => $request->phone ?? '',
                'iban' => $request->iban ?? ''
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            DB::commit();
            return response()->json([
                'success' => true,
                'user' => $user,
                'access_token' => $token
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kayıt işlemi sırasında bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user */
            $user = Auth::user(); // üstteki ile bu değişkenin User modeli olduğunu söylüyoruz.
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Lütfen giriş yapmadan önce e-posta adresinizi doğrulayın.'
                ], 403);
            }
            $access_token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'user' => $user,
                'success' => true,
                'access_token' => $access_token,
            ]);
        }
        return response()->json(['message' => 'Giriş başarısız'], 401);
    }

    public function user()
    {
        if (Auth::check()) {
            $user = User::find(Auth::user()->id);
            return response()->json([
                'id' => $user->id,
                'uid' => $user->uid,
            ], 200);
        }
        return response()->json([], 401);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout successfully.',
        ], 401);
    }
}
