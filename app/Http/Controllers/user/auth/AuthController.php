<?php

namespace App\Http\Controllers\user\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Notifications\user\VerificationCodeNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $plainToken = Str::random(6);
        $hashedToken = Hash::make($plainToken);

        Cache::put("two_factor_code_{$user->registration_number}", $hashedToken, now()->addMinutes(10));
        Cache::put("two_factor_expires_at_{$user->registration_number}", now()->addMinutes(10));

        if (app()->environment('local')) {
            // ローカル環境ならログ出力
            Log::info('認証コード（デバッグ用）: ' . $plainToken);
        } else {
            // 本番環境ならメール送信
            Notification::route('mail', $request->email)
                ->notify(new VerificationCodeNotification($plainToken));
        }


        return response()->json([
            'message' => 'Verification code sent to email',
            'user_id' => $user->registration_number,
        ], 200);
    }
    public function loginVerifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'code' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid request data'], 400);
        }

        $userId = $request->input('user_id');
        $inputCode = $request->input('code');

        // キャッシュキー定義
        $cacheKeyCode = "two_factor_code_{$userId}";
        $cacheKeyExpiresAt = "two_factor_expires_at_{$userId}";

        $cachedCode = Cache::get($cacheKeyCode);
        $expiresAt = Cache::get($cacheKeyExpiresAt);

        if (!$cachedCode || now()->greaterThan($expiresAt)) {
            return response()->json(['message' => 'Verification code expired or not found'], 400);
        }

        if (!Hash::check($inputCode, $cachedCode)) {
            return response()->json(['message' => 'Invalid verification code'], 401);
        }

        Cache::forget($cacheKeyCode);
        Cache::forget($cacheKeyExpiresAt);

        $user = User::where('registration_number', $userId)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Verification successful',
            'user_id' => $userId,
            'user_name' => $user->username,
            'email' => $user->email,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }


    public function forgotPasswordInLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->input('email');

        // ユーザーが存在するかどうかを確認（レスポンスには反映しない）
        $user = DB::table('users')->where('email', $email)->first();

        if ($user) {
            // 認証コード生成＆保存
            $plainToken = Str::random(6);
            $hashedToken = Hash::make($plainToken);

            Cache::put("two_factor_code_{$user->registration_number}", $hashedToken, now()->addMinutes(10));
            Cache::put("two_factor_expires_at_{$user->registration_number}", now()->addMinutes(10));

            // ✅ パスワードリセット用の構造（今回追加）
            Cache::put("password_reset_token_{$email}", [
                'registration_number' => $user->registration_number,
                'code' => $hashedToken,
            ], now()->addMinutes(10));

            if (app()->environment('local')) {
                // ローカル環境ではログに出力
                Log::info('[ForgotPasswordInLogin] 認証コード: ' . $plainToken);
            } else {
                // 本番環境ではメール送信
                Notification::route('mail', $email)
                    ->notify(new VerificationCodeNotification($plainToken));
            }
        } else {
            // 未登録アドレス：warningのみ出力（レスポンスには影響させない）
            Log::warning('[ForgotPasswordInLogin] 未登録メールアドレス: ' . $email);
        }

        // 3秒待機して成功レスポンス（アカウント列挙防止）
        sleep(3);
        return response()->json([
            'success' => true,
            'message' => '処理が完了しました。メールをご確認ください。',
        ]);
    }

    public function verifyResetPasswordCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code'  => ['required', 'string'],
        ]);

        $email = $request->input('email');
        $code = $request->input('code');

        $cached = Cache::get("password_reset_token_{$email}");

        if (!$cached) {
            return response()->json([
                'success' => false,
                'message' => '認証コードが存在しないか、期限切れです。',
            ], 422);
        }

        // ローカル環境のみログ出力
        if (app()->environment('local')) {
            Log::info("[VerifyResetPasswordCode] 入力コード: {$code}");
            Log::info("[VerifyResetPasswordCode] 保存済みハッシュ: {$cached['code']}");
        }

        // 認証コード照合
        if (!Hash::check($code, $cached['code'])) {
            return response()->json([
                'success' => false,
                'message' => '認証コードが一致しません。',
            ], 401);
        }

        // ✅ 認証成功 → 一時的に registration_number を別キャッシュキーに保存
        $registrationNumber = $cached['registration_number'];
        Cache::put("password_reset_verified_{$email}", $registrationNumber, now()->addMinutes(10));

        return response()->json([
            'success' => true,
            'message' => '認証に成功しました。',
        ]);
    }

    public function resetPasswordInLogin(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '入力内容に誤りがあります。',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        // キャッシュから registration_number を取得
        $registrationNumber = Cache::get("password_reset_verified_{$email}");

        if (!$registrationNumber) {
            return response()->json([
                'success' => false,
                'message' => '認証情報が見つかりません。再度やり直してください。',
            ], 403);
        }

        // パスワードを更新
        DB::table('users')
            ->where('registration_number', $registrationNumber)
            ->update([
                'password' => Hash::make($password),
            ]);

        // 使用済みキャッシュ削除
        Cache::forget("password_reset_token_{$email}");
        Cache::forget("password_reset_verified_{$email}");

        return response()->json([
            'success' => true,
            'message' => 'パスワードを再設定しました。',
        ]);
    }




    public function logout(Request $request)
    {
        // 現在の認証済みトークンを削除
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
