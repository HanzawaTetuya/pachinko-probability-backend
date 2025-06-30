<?php

namespace App\Http\Controllers\user\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Notifications\user\VerificationCodeNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserRegistrationController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'date_of_birth' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => '入力内容に誤りがあります。',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (DB::table('users')->where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'このメールアドレスは既に登録されています。',
                'errors' => ['email' => 'すでに使用されています。'],
            ], 409);
        }

        $referralCode = $request->input('referral_code');

        Cache::put("user_data_{$request->email}", [
            'username' => $request->username,
            'email' => $request->email,
            'date_of_birth' => $request->date_of_birth,
            'referral_code' => $referralCode,
        ], now()->addMinutes(10));

        $plainToken = Str::random(6);
        $hashedToken = Hash::make($plainToken);

        Cache::put("two_factor_token_{$request->email}", $hashedToken, now()->addMinutes(10));
        Cache::put("two_factor_expires_at_{$request->email}", now()->addMinutes(10));

        // ✅ 環境によってログ出力を分岐
        if (app()->environment('local', 'development')) {
            Log::info('【デバッグ】認証コード', ['email' => $request->email, 'token' => $plainToken]);
        }

        Notification::route('mail', $request->email)
            ->notify(new VerificationCodeNotification($plainToken));

        return response()->json([
            'message' => '認証コードを発行しました。メールをご確認ください。',
        ], 200);
    }




    public function verifyCode(Request $request)
    {
        $cacheTokenKey = "two_factor_token_{$request->email}";
        $cacheExpiresAtKey = "two_factor_expires_at_{$request->email}";

        $cacheToken = Cache::get($cacheTokenKey);
        $expiresAt = Cache::get($cacheExpiresAtKey);
        $inputToken = $request->input('code');

        if (!$cacheToken || !$expiresAt) {
            return response()->json([
                'message' => '認証情報が見つかりません。もう一度お試しください。',
            ], 400);
        }

        if (Hash::check($inputToken, $cacheToken) && now()->lessThanOrEqualTo($expiresAt)) {
            Cache::forget($cacheTokenKey);
            Cache::forget($cacheExpiresAtKey);

            return response()->json([
                'message' => '認証に成功しました。',
            ], 200);
        }

        return response()->json([
            'message' => '認証コードが無効または期限切れです。',
        ], 400);
    }


    public function addPassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
            ],
            'confirmPassword' => 'required|string|same:password',
        ], [
            'confirmPassword.same' => '確認用パスワードが一致しません。',
        ]);

        $cachedUserData = Cache::get("user_data_{$request->email}");

        if (!$cachedUserData) {
            return response()->json([
                'message' => '一時データが見つかりません。再度お試しください。',
            ], 404);
        }

        $registrationNumber = $this->generateUniqueRegistrationNumber();

        $user = new User();
        $user->registration_number = $registrationNumber;
        $user->name = $cachedUserData['username'];
        $user->email = $cachedUserData['email'];
        $user->date_of_birth = $cachedUserData['date_of_birth'];
        $user->password = Hash::make($request->password);
        $user->status = 'active';

        if (!empty($cachedUserData['referral_code'])) {
            $user->referral_code = $cachedUserData['referral_code'];
        }

        $user->save();

        Cache::forget("user_data_{$request->email}");

        return response()->json([
            'message' => 'ユーザー登録が完了しました。',
            'user' => [
                'registration_number' => $user->registration_number,
                'name' => $user->name,
                'email' => $user->email,
                'date_of_birth' => $user->date_of_birth,
                'referral_code' => $user->referral_code,
                'status' => $user->status,
                'created_at' => $user->created_at,
            ],
        ], 201);
    }



    private function generateUniqueRegistrationNumber()
    {
        do {
            $registrationNumber = random_int(100000, 999999);
        } while (User::where('registration_number', $registrationNumber)->exists());

        return $registrationNumber;
    }
}
