<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Notifications\TwoFactorAuthNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class authController extends Controller
{
    public function showLoginForm()
    {

        return view('admin.login.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            // 二段階認証トークンを生成
            $plainToken = Str::random(6); // 6桁のトークンを生成

            // トークンをハッシュ化してセッションに保存
            $hashedToken = Hash::make($plainToken);
            $request->session()->put('two_factor_token', $hashedToken);
            $request->session()->put('two_factor_expires_at', now()->addMinutes(10)); // 10分間有効

            // ログインしているIPアドレスを取得
            $ipAddress = $request->ip();

            // トークンとIPアドレスを含む通知を送信
            Notification::route('mail', $credentials['email'])
                ->notify(new TwoFactorAuthNotification($plainToken, $ipAddress));

            // 二段階認証画面にリダイレクト
            return redirect()->route('admin.two_factor');
        }

        return back()->with('error', 'メールアドレスもしくはパスワードが違います。');
    }

    public function verifyTwoFactor(Request $request)
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        $sessionToken = $request->session()->get('two_factor_token');
        $expiresAt = $request->session()->get('two_factor_expires_at');
        $inputToken = $request->input('auth-code');


        // ロックアウトされているか確認
        if ($admin->two_factor_lockout_until && now()->lessThan($admin->two_factor_lockout_until)) {
            $remaining = $admin->two_factor_lockout_until->diffForHumans(); // ロック解除までの時間
            return back()->with('error', "アカウントがロックされています。{$remaining}後に再度お試しください。");
        }

        // トークンが一致し、有効期限内か確認
        if (Hash::check($inputToken, $sessionToken) && now()->lessThanOrEqualTo($expiresAt)) {
            // 成功時: 試行回数リセットおよびロック解除
            $admin->two_factor_attempts = 0;
            $admin->two_factor_lockout_until = null;
            $admin->save();  // 更新処理を忘れない

            // トークンを無効化
            $request->session()->forget('two_factor_token');
            $request->session()->forget('two_factor_expires_at');

            return redirect()->route('admin.dashboard')->with('success', '二段階認証に成功しました。');
        }

        // 失敗時: 試行回数の増加
        $admin->increment('two_factor_attempts');  // 試行回数を増加

        // 試行回数が5回を超えたらロックアウト処理
        if ($admin->two_factor_attempts >= 6) {
            // ロックアウト処理 (例: 15分間ロック)
            $lockoutUntil = now()->addMinutes(15);
            $admin->two_factor_attempts = 0;
            $admin->two_factor_lockout_until = $lockoutUntil;
            $admin->save();  // 更新処理を忘れない

            // ログ記録
            Log::warning("管理者ID {$admin->id} が二段階認証に5回失敗し、ロックアウトされました。");

            return back()->with('error', '二段階認証の失敗回数が多すぎます。15分後に再度お試しください。');
        }

        return back()->with('error', '認証トークンが無効です。再度お試しください。');
    }

    public function showDashboard()
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        return view('admin.dashboard', compact('admin'));
    }

    public function logout(Request $request)
    {
        // ログアウト処理
        Auth::guard('admin')->logout();

        // セキュリティのため、セッションを無効化
        $request->session()->invalidate();

        // セッションIDを再生成してセキュリティ強化
        $request->session()->regenerateToken();

        // ログインページへリダイレクト
        return redirect()->route('showLogin')->with('status', 'ログアウトしました。');
    }

}
