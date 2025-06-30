<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Notifications\user\VerificationCodeNotification;

class UserController extends Controller
{
    public function getUserInfo(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            return response()->json([
                'name' => $user->name,
                'email' => $user->email,
                'birth_date' => optional($user->date_of_birth)->format('Y-m-d'),
            ], 200);
        }

        Log::warning('認証に失敗しました');
        return response()->json(['error' => '認証情報が見つかりません。'], 401);
    }


    /**
     * ユーザーネームを編集するメソッド
     */
    public function editUserName(Request $request)
    {
        $user = Auth::user();
        $newUserName = $request->input('username');

        if ($newUserName === $user->name) {
            return response()->json([
                'success' => false,
                'message' => '現在のユーザー名と同じです。',
            ], 400);
        }

        $request->validate([
            'username' => [
                'required',
                'string',
                'min:8',
                'unique:users,name,' . $user->id,
            ],
        ], [
            'username.required' => 'ユーザー名は必須です。',
            'username.string' => 'ユーザー名は文字列である必要があります。',
            'username.min' => 'ユーザー名は8文字以上である必要があります。',
            'username.unique' => 'このユーザー名は既に使用されています。',
        ]);

        $user->name = $newUserName;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'ユーザー名を更新しました。',
            'username' => $user->name,
        ], 200);
    }




    public function editEmail(Request $request)
    {
        $user = Auth::user();
        $userId = $user->registration_number;
        $email = $request->input('email');

        if ($email === $user->email) {
            return response()->json([
                'success' => false,
                'message' => 'すでに登録されているメールアドレスです。',
            ], 400);
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        Cache::put("user_email_{$userId}", $email, now()->addMinutes(10));

        $plainToken = Str::random(6);
        $hashedToken = Hash::make($plainToken);

        Cache::put("two_factor_token_{$userId}", $hashedToken, now()->addMinutes(10));
        Cache::put("two_factor_expires_at_{$userId}", now()->addMinutes(10));

        // 通知は必要に応じて実装
        Notification::route('mail', $email)
            ->notify(new VerificationCodeNotification($plainToken));

        return response()->json([
            'success' => true,
            'message' => '確認コードを送信しました。',
            'username' => $user->email,
        ], 200);
    }


    public function verifyEmailCode(Request $request)
    {
        $user = Auth::user();
        $userId = $user->registration_number;

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $code = $request->input('code');
        $cachedToken = Cache::get("two_factor_token_{$userId}");
        $expiresAt = Cache::get("two_factor_expires_at_{$userId}");
        $email = Cache::get("user_email_{$userId}");

        if (!$cachedToken || !$expiresAt || now()->greaterThan($expiresAt)) {
            return response()->json(['error' => '認証コードの有効期限が切れています。'], 400);
        }

        if (!Hash::check($code, $cachedToken)) {
            return response()->json(['error' => '認証コードが一致しません。'], 400);
        }

        $user->email = $email;
        $user->save();

        Cache::forget("two_factor_token_{$userId}");
        Cache::forget("two_factor_expires_at_{$userId}");
        Cache::forget("user_email_{$userId}");

        Log::info('メールアドレス更新成功', ['user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'message' => 'メールアドレスの変更を完了しました。',
        ], 200);
    }

    public function editPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user = Auth::user();
        $userId = $user->registration_number;

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'パスワードが正しくありません。'], 401);
        }

        $plainToken = Str::random(6);
        $hashedToken = Hash::make($plainToken);

        Cache::put("two_factor_code_{$userId}", $hashedToken, now()->addMinutes(10));
        Cache::put("two_factor_expires_at_{$userId}", now()->addMinutes(10));

        // デバッグモードのときだけログ出力
        if (config('app.debug')) {
            Log::info('パスワード認証コード発行', [
                'user_id' => $userId,
                'token' => $plainToken,
            ]);
        }

        // メール通知を送信（有効化する場合）
        Notification::route('mail', $user->email)
            ->notify(new VerificationCodeNotification($plainToken));

        return response()->json([
            'message' => 'パスワードの認証が完了しました。認証コードをメールで送信しました。',
        ], 200);
    }



    public function verifyPasswordCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = Auth::user();
        $userId = $user->registration_number;

        $inputCode = $request->input('code');
        $cachedCode = Cache::get("two_factor_code_{$userId}");
        $expiresAt = Cache::get("two_factor_expires_at_{$userId}");

        if (!$cachedCode || now()->greaterThan($expiresAt)) {
            return response()->json(['message' => '認証コードの有効期限が切れています。'], 400);
        }

        if (!Hash::check($inputCode, $cachedCode)) {
            return response()->json(['message' => '認証コードが一致しません。'], 401);
        }

        Cache::forget("two_factor_code_{$userId}");
        Cache::forget("two_factor_expires_at_{$userId}");

        Log::info('パスワード認証コードの検証成功', ['user_id' => $userId]);

        return response()->json([
            'message' => '認証が完了しました。',
            'status' => "success",
            'next' => "reset_password"
        ], 200);
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'confirmed',
            ],
        ]);

        $user = Auth::user();

        if (!$user) {
            Log::error('ユーザー情報が見つかりませんでした。');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        Log::info('パスワード更新成功', ['user_id' => $user->id]);

        return response()->json([
            'message' => 'パスワードの更新が完了しました。',
        ], 200);
    }


    public function forgotPasswordInMypage(Request $request)
    {
        $user = Auth::user();
        $userId = $user->registration_number;

        $plainToken = Str::random(6);
        $hashedToken = Hash::make($plainToken);

        Cache::put("two_factor_code_{$userId}", $hashedToken, now()->addMinute(10));
        Cache::put("two_factor_expires_at_{$userId}", now()->addMinute(10));

        // デバッグモードのときだけログ出力
        if (config('app.debug')) {
            Log::info('マイページ用認証コード発行', [
                'user_id' => $userId,
                'token' => $plainToken,
            ]);
        }

        // メール通知を送信（有効化する場合）
        Notification::route('mail', $user->email)
            ->notify(new VerificationCodeNotification($plainToken));

        return response()->json([
            'message' => '認証コードを送信しました。',
        ], 200);
    }
}
