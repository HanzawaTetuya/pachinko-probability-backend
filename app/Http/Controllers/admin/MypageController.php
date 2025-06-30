<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Notifications\TwoFactorAuthNotification;
use App\Notifications\UpdateNotification;
use Illuminate\Support\Facades\Notification;



class MypageController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        return view('admin.mypage.login', compact('admin'));
    }

    // マイページへのログイン処理
    public function mypageLogin(Request $request)
    {
        // バリデーションの追加
        $request->validate([
            'password' => 'required|string',
        ]);

        $admin = Auth::guard('admin')->user();

        // 入力されたパスワードが正しいかチェック
        if (Auth::guard('admin')->attempt(['email' => $admin->email, 'password' => $request->password])) {
            // 二段階認証トークンを生成
            $plainToken = Str::random(6); // 6桁のトークンを生成

            // トークンをハッシュ化してセッションに保存
            $hashedToken = Hash::make($plainToken);
            $request->session()->put('two_factor_token', $hashedToken);
            $request->session()->put('two_factor_expires_at', now()->addMinutes(10)); // 10分間有効

            // 管理者のメールアドレス宛にトークンとIPアドレスを含む通知を送信
            $ipAddress = $request->ip(); // ログイン中のIPアドレスを取得
            Notification::route('mail', $admin->email)
                ->notify(new TwoFactorAuthNotification($plainToken, $ipAddress));

            // パスワードが正しければ認証画面にリダイレクト
            return redirect()->route('admin.mypage.two_factor');
        }

        // パスワードが間違っている場合、エラーメッセージを表示
        return back()->with('error', 'パスワードが違います。');
    }

    //二段階認証画面の表示
    public function showTwoFactor()
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        return view('admin.mypage.two_factor', compact('admin'));
    }

    // 二段階認証コードの検証
    public function verifyCode(Request $request)
    {
        $sessionToken = $request->session()->get('two_factor_token');
        $expiresAt = $request->session()->get('two_factor_expires_at');
        $inputToken = $request->input('auth-code');

        // トークンが一致し、有効期限内か確認
        if (Hash::check($inputToken, $sessionToken) && now()->lessThanOrEqualTo($expiresAt)) {
            // トークンを無効化
            $request->session()->forget('two_factor_token');
            $request->session()->forget('two_factor_expires_at');

            // 成功メッセージをフラッシュセッションに保存
            $request->session()->flash('success', '二段階認証に成功しました！');

            // ダッシュボードへリダイレクト
            return redirect()->route('admin.mypage');
        }

        return back()->with('error', '二段階認証トークンが無効です。再度お試しください。');
    }

    // マイページの表示
    public function showMypage()
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        return view('admin.mypage.show', compact('admin'));
    }

    public function showEditTwoFactor(Request $request)
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        $type = $request->query('type');  // `type` パラメータを取得

        // 有効期限の確認、トークンがない、または有効期限が過ぎている場合は再生成
        if (!$request->session()->has('two_factor_token') || now()->greaterThan($request->session()->get('two_factor_expires_at'))) {
            // 二段階認証トークンを生成
            $plainToken = Str::random(6); // 6桁のトークンを生成

            // トークンをハッシュ化してセッションに保存
            $hashedToken = Hash::make($plainToken);
            $request->session()->put('two_factor_token', $hashedToken);
            $request->session()->put('two_factor_expires_at', now()->addMinutes(10)); // 10分間有効
        }

        // セッションから既存のトークンを取得
        $twoFactorToken = $request->session()->get('two_factor_token');

        // 管理者のメールアドレス宛にトークンとIPアドレスを含む通知を送信
        $ipAddress = $request->ip(); // ログイン中のIPアドレスを取得
        Notification::route('mail', $admin->email)
            ->notify(new TwoFactorAuthNotification($plainToken, $ipAddress));

        // 二段階認証ページにトークンと管理者情報を渡す
        return view('admin.mypage.edit_two_factor', compact('type', 'admin', 'twoFactorToken'));
    }

    // 二段階認証コードの検証
    public function verifyEditTwoFactor(Request $request)
    {
        $sessionToken = $request->session()->get('two_factor_token');
        $expiresAt = $request->session()->get('two_factor_expires_at');
        $inputToken = $request->input('auth-code');

        // トークンが一致し、有効期限内か確認
        if (Hash::check($inputToken, $sessionToken) && now()->lessThanOrEqualTo($expiresAt)) {
            // トークンを無効化
            $request->session()->forget('two_factor_token');
            $request->session()->forget('two_factor_expires_at');

            // 成功メッセージをフラッシュセッションに保存
            $request->session()->flash('success', '二段階認証に成功しました！');

            // POSTされた`type`を取得
            $type = $request->input('type');

            return redirect()->route('admin.mypage.edit', ['type' => $type]);
        }

        return back()->with('error', '二段階認証トークンが無効です。再度お試しください。');
    }

    public function showEditPage($type)
    {
        $admin = Auth::guard('admin')->user();

        switch ($type) {
            case 'name':
                return view('admin.mypage.username_edit', compact('admin', 'type'));
            case 'email':
                return view('admin.mypage.email_edit', compact('admin', 'type'));
            case 'password':
                return view('admin.mypage.password_edit', compact('admin', 'type'));
            default:
                abort(404); // 不正なtypeの場合は404エラー
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        $type = $request->input('type');  // 更新する項目の種類（name, email, password）

        switch ($type) {
            case 'name':
                $admin->name = $request->input('name');
                break;

            case 'password':
                $admin->password = Hash::make($request->input('password'));  // パスワードをハッシュ化して保存
                break;

            default:
                return back()->with('error', '無効な更新リクエストです。');
        }

        $admin->save();  // 管理者情報を保存

        Notification::route('mail', $admin->email)
            ->notify(new UpdateNotification($type));

        return redirect()->route('admin.mypage')->with('success', 'プロフィールが更新されました。');
    }

    public function verifyEmail(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $email = $request->input('email');

        $plainToken = Str::random(6); // 6桁のトークンを生成

        // トークンをハッシュ化してセッションに保存
        $hashedToken = Hash::make($plainToken);
        $request->session()->put('two_factor_token', $hashedToken);
        $request->session()->put('two_factor_expires_at', now()->addMinutes(10)); // 10分間有効

        // 管理者のメールアドレス宛にトークンとIPアドレスを含む通知を送信
        $ipAddress = $request->ip(); // ログイン中のIPアドレスを取得
        Notification::route('mail', $admin->email)
            ->notify(new TwoFactorAuthNotification($plainToken, $ipAddress));

        // セッションに新しいメールアドレスを保存
        $request->session()->put('new_email', $email);

        // 認証コード入力ページにリダイレクト
        return redirect()->route('admin.email.verify.show')->with('email', $email);
    }


    public function verifyEmailShow()
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        $email = session('new_email');  // セッションから新しいメールアドレスを取得

        if (!$email) {
            return redirect()->route('admin.mypage')->with('error', 'メールアドレスが指定されていません。');
        }

        return view('admin.mypage.email_two_factor', compact('admin', 'email'));  // 認証コード入力ページを表示
    }

    public function verifyCodeEmail(Request $request)
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        $sessionToken = $request->session()->get('two_factor_token');  // セッションからトークンを取得
        $expiresAt = $request->session()->get('two_factor_expires_at');  // トークンの有効期限を取得
        $inputToken = $request->input('auth-code');

        // 1. トークンがセッションに存在するか確認
        if (!$sessionToken) {
            return back()->with('error', '認証トークンがセッションに存在しません。再度お試しください。');
        }

        // 2. ユーザーが入力したコードとセッションのトークンが一致するか確認
        if (!Hash::check($inputToken, $sessionToken)) {
            return back()->with('error', '認証コードが一致しません。再度お試しください。');
        }

        // 3. トークンの有効期限が切れていないか確認
        if (now()->greaterThan($expiresAt)) {
            return back()->with('error', '認証トークンの有効期限が切れています。再度お試しください。');
        }

        // 4. セッションからnew_emailを取得
        $newEmail = $request->session()->get('new_email');

        // new_emailがセッションに存在しない場合、エラーメッセージを返す
        if (!$newEmail) {
            return redirect()->route('admin.mypage')->with('error', '新しいメールアドレスが指定されていません。');
        }

        try {
            // 5. 管理者のメールアドレスを更新
            $admin->email = $newEmail;
            $admin->save();

            // 6. セッションのクリーンアップ
            $request->session()->forget('two_factor_token');
            $request->session()->forget('two_factor_expires_at');
            $request->session()->forget('new_email');

            // メールアドレス変更通知を送信
            Notification::route('mail', $admin->email)
                ->notify(new UpdateNotification('email'));  // 'email'を直接指定

            // 成功時のリダイレクト
            return redirect()->route('admin.mypage')->with('success', 'Eメールを変更しました。');
        } catch (\Exception $e) {
            // エラー時の処理
            Log::error('Email update failed: ' . $e->getMessage());  // エラーログを記録

            return redirect()->route('admin.mypage')->with('error', '予期せぬエラーが発生しました。変更をやり直してください。');
        }
    }
}
