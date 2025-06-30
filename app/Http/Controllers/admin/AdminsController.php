<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LoginRequestNotification;
use App\Notifications\TwoFactorAuthNotification;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;

class AdminsController extends Controller
{
    // name(show.admins.login)
    public function showLogin()
    {
        Log::info('ログのテスト');
        
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        return view('admin.admins.login', compact('admin'));
    }

    // name(admins.login.request)
    public function loginRequest(Request $request)
    {
        // バリデーション
        $request->validate([
            'password' => 'required|string|min:8',
            'reason' => 'required|string',
        ]);

        // ログイン中のユーザーを取得
        $admin = Auth::guard('admin')->user();
        $name = $admin->name;
        $email = $admin->email;
        $reason = $request->reason;
        $kinds = '登録者リストの閲覧';

        // パスワードの確認
        if (!Hash::check($request->password, $admin->password)) {
            return back()->with('error', 'パスワードが正しくありません。');
        }

        // 認証コードを生成
        $plainToken = Str::random(6); // 6桁のトークンを生成

        // トークンをログに出力
        Log::info("認証コード: {$plainToken} (管理者: {$name}, メール: {$email})");

        // トークンをハッシュ化してセッションに保存
        $hashedToken = Hash::make($plainToken);
        $request->session()->put('two_factor_token', $hashedToken);
        $request->session()->put('two_factor_expires_at', now()->addMinutes(10)); // 10分間有効
        $request->session()->put('plain_token', $plainToken); // リリース時削除

        $adminAddress = env('NOTIFICATION_EMAIL');

        // ユーザーに認証コードを送信
        Notification::route('mail', $adminAddress)
            ->notify(new LoginRequestNotification($name, $email, $plainToken, $reason, $kinds));

        // 認証コード入力画面にリダイレクト
        return redirect()->route('admins.two.factor', ['admin' => $admin]);
    }

    // users.two.factor
    public function showTwoFactor()
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        return view('admin.admins.two_factor', compact('admin'));
    }

    // name('admins.verify.two.factor')
    public function verifyTwoFactor(Request $request)
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        $sessionToken = $request->session()->get('two_factor_token');
        $expiresAt = $request->session()->get('two_factor_expires_at');
        $inputToken = $request->input('auth-code');

        // トークンが一致し、有効期限内か確認
        if (Hash::check($inputToken, $sessionToken) && now()->lessThanOrEqualTo($expiresAt)) {

            // トークンを無効化
            $request->session()->forget('two_factor_token');
            $request->session()->forget('two_factor_expires_at');

            // 成功メッセージをフラッシュセッションに保存
            $request->session()->flash('success', '閲覧が承認されました。');

            return redirect()->route('admins.show', ['admin' => $admin]);
        }

        return back()->with('error', '二段階認証トークンが無効です。再度お試しください。');
    }

    // name('admins.show')
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得

        // ユーザークエリの作成
        $query = Admin::query();

        // フィルター条件がセットされている場合、フィルタリングを実行
        if ($request->filled('filter') && $request->filled('search')) {
            $filter = $request->input('filter');
            $search = $request->input('search');

            switch ($filter) {
                case 'name':
                    $query->where('name', 'LIKE', '%' . $search . '%');
                    break;
                case 'email':
                    $query->where('email', 'LIKE', '%' . $search . '%');
                    break;
                case 'authority':
                    $query->where('authority', 'LIKE', '%' . $search . '%');  // 権限の検索
                    break;
                case 'created_at':
                    $query->whereDate('created_at', $search);  // 登録日での検索
                    break;
                case 'updated_at':
                    $query->whereDate('updated_at', $search);  // 更新日での検索
                    break;
                case 'birthday':
                    $query->whereDate('birthday', $search);  // 誕生日での検索
                    break;
                default:
                    // 何もしない（全検索）
                    break;
            }
        }

        // ページネーション（1ページ20件）
        $admins = $query->paginate(20);

        // ビューにデータを渡す
        return view('admin.admins.index', compact('admin', 'admins'));
    }

    // name('admin.show')
    public function show($id)
    {
        $user = Admin::findOrFail($id);

        // 管理者情報
        $admin = Auth::guard('admin')->user();

        // ビューにデータを渡して表示
        return view('admin.admins.show', compact('user', 'admin'));
    }

    // name('admin.edit.button')
    public function editTwoFactorCode(Request $request, $id)
    {
        // 管理者情報
        $admin = Auth::guard('admin')->user();

        $plainToken = Str::random(6); // 6桁のトークンを生成

        // トークンをハッシュ化してセッションに保存
        $hashedToken = Hash::make($plainToken);
        $request->session()->put('two_factor_token', $hashedToken);
        $request->session()->put('two_factor_expires_at', now()->addMinutes(10)); // 10分間有効

        $ipAddress = $request->ip(); // ログイン中のIPアドレスを取得
        Notification::route('mail', $admin->email)
            ->notify(new TwoFactorAuthNotification($plainToken, $ipAddress));

        return redirect()->route('admin.edit.two_factor', ['admin' => $admin, $id]);
    }

    // name('admin.edit.two_factor')
    public function showEditTwoFactor($id)
    {
        $user = Admin::findOrFail($id);

        // 管理者情報
        $admin = Auth::guard('admin')->user();

        return view('admin.admins.edit_two_factor', compact('admin', 'user'));
    }


    public function verifyEditTwoFactor(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        $user = Admin::findOrFail($id);

        $sessionToken = $request->session()->get('two_factor_token');
        $expiresAt = $request->session()->get('two_factor_expires_at');
        $inputToken = $request->input('auth-code');

        // トークンが一致し、有効期限内か確認
        if (Hash::check($inputToken, $sessionToken) && now()->lessThanOrEqualTo($expiresAt)) {

            // トークンを無効化
            $request->session()->forget('two_factor_token');
            $request->session()->forget('two_factor_expires_at');

            // 成功メッセージをフラッシュセッションに保存
            $request->session()->flash('success', '閲覧が承認されました。');

            // $admin と $user の両方をリダイレクト先に渡す
            return redirect()->route('admin.edit', ['id' => $user->id, 'admin' => $admin->id]);
        }

        return back()->with('error', '二段階認証トークンが無効です。再度お試しください。');
    }

    public function edit($id)
    {

        $admin = Auth::guard('admin')->user();
        // 対象のユーザーを取得
        $user = Admin::findOrFail($id);

        // 編集画面を表示
        return view('admin.admins.edit', compact('user', 'admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        // バリデーション
        $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|unique:admins,email,' . $admin->id,
            'authority' => 'required|in:administrator,editor,viewer',  // authorityのバリデーション追加
        ]);

        // 管理者情報の更新
        $admin->name = $request->input('user_name');
        $admin->email = $request->input('user_email');
        $admin->authority = $request->input('authority');  // authorityの更新

        // データ保存
        $admin->save();

        return redirect()->route('admin.show', ['id' => $admin->id])
            ->with('success', 'ユーザー情報が更新されました。');
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.admins.store', compact('admin'));
    }

    public function store(Request $request)
    {
        // 管理者情報
        $admin = Auth::guard('admin')->user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed', // password_confirmationを使用
            'birthday' => 'required|date',
        ]);

        $hashedPassword = Hash::make($validatedData['password']);

        $plainToken = Str::random(6); // 6桁のトークンを生成

        // トークンをハッシュ化してセッションに保存
        $hashedToken = Hash::make($plainToken);
        $request->session()->put('two_factor_token', $hashedToken);
        $request->session()->put('two_factor_expires_at', now()->addMinutes(10)); // 10分間有効

        // セッションに保存
        session([
            'temporary_user' => [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'birthday' => $validatedData['birthday'],
                'password' => $hashedPassword,
                'two_factor_token' => $hashedToken,
                'two_factor_expires_at' => now()->addMinutes(10), // 10分有効
            ]
        ]);

        $ipAddress = $request->ip(); // ログイン中のIPアドレスを取得
        Notification::route('mail', $admin->email)
            ->notify(new TwoFactorAuthNotification($plainToken, $ipAddress));

        return redirect()->route('admin.store.two.factor', ['admin' => $admin]);
    }

    public function storeTwoFactor()
    {
        // 管理者情報
        $admin = Auth::guard('admin')->user();
        return view('admin.admins.show_two_factor', compact('admin'));
    }

    public function verifyStoreTwoFactor(Request $request)
    {
        // セッションから仮登録ユーザー情報を取得
        $temporaryUser = session('temporary_user');

        if (!$temporaryUser) {
            return redirect()->route('admin.create')->with('error', 'セッションが切れました。再度登録を行ってください。');
        }

        // セッションから認証コードと有効期限を取得
        $sessionToken = $request->session()->get('two_factor_token');
        $expiresAt = $request->session()->get('two_factor_expires_at');
        $inputToken = $request->input('auth-code');

        // 認証コードが有効期限内かチェック
        if (now()->greaterThan($expiresAt)) {
            return redirect()->route('admin.create')->with('error', '認証コードの有効期限が切れています。再度登録を行ってください。');
        }

        // 認証コードの一致確認
        if (!Hash::check($inputToken, $sessionToken)) {
            return back()->with('error', '認証コードが一致しません。再度お試しください。');
        }

        // 認証成功: ユーザー登録処理
        Admin::create([
            'name' => $temporaryUser['name'],
            'email' => $temporaryUser['email'],
            'birthday' => $temporaryUser['birthday'],
            'password' => $temporaryUser['password'],  // ハッシュ化済みのパスワード
            'authority' => 'viewer',
        ]);

        // セッションから仮登録データを削除
        session()->forget('temporary_user');
        session()->forget('two_factor_token');
        session()->forget('two_factor_expires_at');

        return redirect()->route('admins.show')->with('success', '管理者の登録が完了しました。');
    }
}
