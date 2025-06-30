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
use App\Models\User;

class UsersController extends Controller
{
    public function showLogin()
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        return view('admin.users.login', compact('admin'));
    }

    public function loginRequest(Request $request)
    {
        // バリデーション
        $request->validate([
            'password' => 'required|string|min:8',
            'reason' => 'required|string',
        ]);

        // ログイン中のユーザーを取得
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
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

        // トークンをハッシュ化してセッションに保存
        $hashedToken = Hash::make($plainToken);
        $request->session()->put('two_factor_token', $hashedToken);
        $request->session()->put('two_factor_expires_at', now()->addMinutes(10)); // 10分間有効

        $adminAddress = env('NOTIFICATION_EMAIL');

        // ユーザーに認証コードを送信
        Notification::route('mail', $adminAddress)
            ->notify(new LoginRequestNotification($name, $email, $plainToken, $reason, $kinds));

        // 今後IPアドレスの取得も行う

        // 認証コード入力画面にリダイレクト
        return redirect()->route('users.two.factor', ['admin' => $admin]);
    }

    public function showTwoFactor()
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        return view('admin.users.two_factor', compact('admin'));
    }

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

            return redirect()->route('users.show', ['admin' => $admin]);
        }

        return back()->with('error', '二段階認証トークンが無効です。再度お試しください。');
    }

    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得

        // ユーザークエリの作成
        $query = User::query();

        // フィルター条件がセットされている場合、フィルタリングを実行
        if ($request->filled('filter') && $request->filled('search')) {
            $filter = $request->input('filter');
            $search = $request->input('search');

            switch ($filter) {
                case 'registration_number':
                    $query->where('registration_number', 'LIKE', '%' . $search . '%');
                    break;
                case 'name':
                    $query->where('name', 'LIKE', '%' . $search . '%');
                    break;
                case 'email':
                    $query->where('email', 'LIKE', '%' . $search . '%');
                    break;
                case 'created_at':
                    $query->whereDate('created_at', $search);  // 日付検索
                    break;
                case 'updated_at':
                    $query->whereDate('updated_at', $search);  // 日付検索
                    break;
                case 'status':
                    $query->where('status', $search);
                    break;
                default:
                    // 何もしない（全検索）
                    break;
            }
        }

        // ページネーション（1ページ20件）
        $users = $query->paginate(20);

        // ビューにデータを渡す
        return view('admin.users.index', compact('admin', 'users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        // ユーザーに関連する注文を取得
        $orders = $user->orders;  // Userモデルのorders()リレーションを使用

        // 管理者情報
        $admin = Auth::guard('admin')->user();

        // ビューにデータを渡して表示
        return view('admin.users.show', compact('user', 'admin', 'orders'));
    }

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

        return redirect()->route('admin.user.edit.two_factor', ['admin' => $admin, $id]);
    }

    public function showEditTwoFactor($id)
    {
        $user = User::findOrFail($id);

        // 管理者情報
        $admin = Auth::guard('admin')->user();

        return view('admin.users.edit_two_factor', compact('admin', 'user'));
    }

    public function verifyEditTwoFactor(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        $user = User::findOrFail($id);
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

            return redirect()->route('admin.user.edit', ['id' => $user->id]);
        }

        return back()->with('error', '二段階認証トークンが無効です。再度お試しください。');
    }

    public function edit($id)
    {

        $admin = Auth::guard('admin')->user();
        // 対象のユーザーを取得
        $user = User::findOrFail($id);

        // 編集画面を表示
        return view('admin.users.edit', compact('user', 'admin'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // バリデーション
        $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        // ユーザー情報の更新
        $user->name = $request->input('user_name');
        $user->email = $request->input('user_email');
        $user->status = $request->has('status') ? 'active' : 'inactive';  // チェックボックスの状態に基づいてステータスを更新

        $user->save();

        return redirect()->route('admin.user.show', ['id' => $user->id])->with('success', 'ユーザー情報が更新されました。');
    }

    public function create()
    {
        return view('admin.users.store', compact('admin'));
    }

    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'date_of_birth' => 'required|date',
            'password' => 'required|string|min:8|confirmed',  // パスワード確認用のルール
        ]);

        // 仮登録（ここでデータベースに保存せず、メールを送信）
        $user = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'date_of_birth' => $request->input('date_of_birth'),
            'password' => Hash::make($request->input('password')),
            'status' => 'pending',  // 仮登録のステータス
        ]);

        // 管理者とユーザーに二段階認証メールを送信
        $plainToken = Str::random(6);
        $hashedToken = Hash::make($plainToken);
        $request->session()->put('two_factor_token', $hashedToken);
        $request->session()->put('two_factor_expires_at', now()->addMinutes(10));

        // ユーザーと管理者に通知
        Notification::route('mail', $user->email)
            ->notify(new TwoFactorAuthNotification($plainToken));

        Notification::route('mail', Auth::guard('admin')->user()->email)
            ->notify(new TwoFactorAuthNotification($plainToken));


        return redirect()->route('admin.user.two_factor');
    }
}
