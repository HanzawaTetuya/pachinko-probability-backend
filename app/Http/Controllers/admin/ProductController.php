<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LoginRequestNotification;
use App\Models\LoginRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function showLogin()
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        return view('admin.products.login', compact('admin'));
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
        // $request->session()->put('two_factor_token', $plainToken);
        $request->session()->put('two_factor_expires_at', now()->addMinutes(10)); // 10分間有効

        $adminAddress = env('NOTIFICATION_EMAIL');

        // ユーザーに認証コードを送信
        // Notification::route('mail', $adminAddress)
        //     ->notify(new LoginRequestNotification($name, $email, $plainToken, $reason, $kinds));

        // 今後IPアドレスの取得も行う

        Log::info('ログイントークン:', ['login_token' => $plainToken]);

        // 認証コード入力画面にリダイレクト
        return redirect()->route('products.two.factor', ['admin' => $admin]);
    }

    public function showTwoFactor()
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        return view('admin.products.two_factor', compact('admin'));
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

            return redirect()->route('products.show', ['admin' => $admin]);
        }

        return back()->with('error', '二段階認証トークンが無効です。再度お試しください。');
    }

    public function showProducts(Request $request)
    {
        // クエリの作成
        $query = Product::query();

        // フィルター条件がセットされている場合、フィルタリングを実行
        if ($request->filled('filter') && $request->filled('search')) {
            $filter = $request->input('filter');
            $search = $request->input('search');

            switch ($filter) {
                case 'name':
                    $query->where('name', 'LIKE', '%' . $search . '%');
                    break;
                case 'product_number':
                    $query->where('product_number', 'LIKE', '%' . $search . '%');
                    break;
                case 'price':
                    $query->where('price', $search);
                    break;
                case 'manufacturer':
                    $query->where('manufacturer', 'LIKE', '%' . $search . '%');
                    break;
                case 'category':
                    $query->where('category', 'LIKE', '%' . $search . '%');
                    break;
                case 'is_published':
                    // 公開か非公開の条件。`公開中`の場合は1, `非公開`の場合は0。
                    $query->where('is_published', $search == '公開中' ? 1 : 0);
                    break;
                default:
                    // 何もしない（全検索）
                    break;
            }
        }

        // 商品を取得
        $products = $query->paginate(20);
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        return view('admin.products.index', compact('admin', 'products'));
    }

    public function storeProduct(Request $request)
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        $name = $admin->name;
        $email = $admin->email;
        $reason = $request->reason;
        $kinds = '商品の追加';

        $plainToken = Str::random(6); // 6桁のトークンを生成

        // トークンをハッシュ化してセッションに保存
        $hashedToken = Hash::make($plainToken);
        $request->session()->put('two_factor_token', $hashedToken);
        $request->session()->put('two_factor_expires_at', now()->addMinutes(10)); // 10分間有効

        // $ipAddress = $request->ip(); // ログイン中のIPアドレスを取得
        $adminAddress = env('NOTIFICATION_EMAIL');

        // ユーザーに認証コードを送信
        Notification::route('mail', $adminAddress)
            ->notify(new LoginRequestNotification($name, $email, $plainToken, $reason, $kinds));


        return redirect()->route('products.store.show.two.factor');
    }

    public function showTwoFactorForm()
    {
        return view('admin.products.store_two_factor');
    }

    public function verifyTwoFactorCode()
    {
        // コードの検証
    }
    public function showStoreForm()
    {
        return view('admin.products.store');
    }

    public function storeTemporary(Request $request)
    {
        // バリデーションルールの定義
        $rules = [
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 画像ファイルの種類とサイズを指定
            'product_name' => 'required|string|max:255',
            'maker_name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric',
            'release_date' => 'required|date',
            'internal_storage' => 'required|file',
            'product_description' => 'nullable|string',
        ];

        // カスタムバリデーションメッセージの定義
        $messages = [
            'product_image.required' => '機種画像をアップロードしてください。',
            'product_image.image' => '機種画像は有効な画像形式でなければなりません。',
            'product_name.required' => '商品名を入力してください。',
            'maker_name.required' => 'メーカー名を入力してください。',
            'category.required' => 'カテゴリーを入力してください。',
            'price.required' => '金額を入力してください。',
            'price.numeric' => '金額は数値で入力してください。',
            'release_date.required' => '発売日を入力してください。',
            'release_date.date' => '発売日は有効な日付形式で入力してください。',
            'internal_storage.required' => 'Pythonファイルをアップロードしてください。',
            'internal_storage.file' => 'Pythonファイルは有効なファイル形式でなければなりません。',
            'product_description.required' => '商品の説明を入力してください。',

        ];

        // バリデーションを実行
        $validatedData = $request->validate($rules, $messages);

        // アップロードされた画像ファイルを一時的に保存し、そのパスを取得
        $productImagePath = $request->file('product_image')->store('temp', 'public');

        // 内部計算データ（Pythonファイル）も同様に保存
        $internalStoragePath = $request->file('internal_storage')->store('temp', 'private');
        Log::info('Pythonファイルの保存パス:', ['path' => $internalStoragePath]);

        // 入力データをセッションに保存
        $request->session()->put([
            'product_image_path' => $productImagePath,
            'product_name' => $request->product_name,
            'maker_name' => $request->maker_name,
            'category' => $request->category,
            'price' => $request->price,
            'release_date' => $request->release_date,
            'internal_storage_path' => $internalStoragePath,
            'product_description' => $request->product_description,
            'is_published' => $request->is_published,
        ]);
        // 確認画面にリダイレクト
        return redirect()->route('products.store.confirm');
    }

    public function confirmProduct()
    {
        return view('admin.products.store_comfirm');
    }

    public function createProduct(Request $request)
    {
        // セッションからデータを取得して保存
        $product = new Product();
        $product->name = session('product_name');
        $product->manufacturer = session('maker_name');
        $product->category = session('category');
        $product->price = session('price');
        $product->release_date = session('release_date');
        $product->python_file_path = session('internal_storage_path');
        $product->description = session('product_description');
        $product->is_published = session('is_published');
        $product->image_path = session('product_image_path');
        $product->save();

        // セッションをクリア
        $request->session()->forget([
            'product_image_path',
            'product_name',
            'maker_name',
            'category',
            'price',
            'release_date',
            'internal_storage_path',
            'product_description',
            'is_published',
        ]);

        return redirect()->route('products.show')->with('success', '商品が登録されました。');
    }

    public function productShow($id)
    {
        $product = Product::findOrFail($id); // IDに基づいて商品を取得、見つからない場合はエラー
        return view('admin.products.show', compact('product')); // `products.show` というビューに渡す
    }


    public function editTwoFactor(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得
        $product = Product::findOrFail($id);

        $name = $admin->name;
        $email = $admin->email;
        $reason = $request->reason;
        $kinds = '商品の編集';

        // 認証コードを生成
        $plainToken = Str::random(6); // 6桁のトークンを生成

        // トークンをハッシュ化してセッションに保存
        $hashedToken = Hash::make($plainToken);
        $request->session()->put('two_factor_token', $hashedToken);
        // $request->session()->put('two_factor_token', $plainToken);
        $request->session()->put('two_factor_expires_at', now()->addMinutes(10)); // 10分間有効

        $adminAddress = env('NOTIFICATION_EMAIL');

        // ユーザーに認証コードを送信
        Notification::route('mail', $adminAddress)
            ->notify(new LoginRequestNotification($name, $email, $plainToken, $reason, $kinds));

        // 今後IPアドレスの取得も行う

        // 認証コード入力画面にリダイレクト
        return redirect()->route('product.edit.show.two.factor', ['id' => $id]);
    }

    public function showEditTwoFactor($id)
    {
        // 指定されたIDの商品を取得する
        $product = Product::findOrFail($id);

        // 管理者情報を取得（必要であれば）
        $admin = Auth::guard('admin')->user();
        return view('admin.products.edit_two_factor', compact('product', 'admin'));
    }

    public function editVerifyCode(Request $request, $id)
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
            $request->session()->flash('success', '閲覧が承認されました。');

            return redirect()->route('product.edit.show', ['id' => $id]);
        }

        return back()->with('error', '二段階認証トークンが無効です。再度お試しください。');
    }



    public function showEdit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }
    public function editTemporary(Request $request, $id)
    {
        // バリデーションルールの定義
        $rules = [
            'product_name' => 'required|string|max:255',
            'maker_name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric',
            'release_date' => 'required|date',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 必須ではなく変更された場合のみ
            'internal_storage' => 'nullable|file', // 必須ではなく変更された場合のみ
            'product_description' => 'nullable|string',
        ];

        // ファイルがアップロードされている場合にのみ、画像とPythonファイルのバリデーションを追加
        if ($request->hasFile('product_image')) {
            $rules['product_image'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'; // 画像ファイルの種類とサイズを指定
        }

        if ($request->hasFile('internal_storage')) {
            $rules['internal_storage'] = 'file'; // 内部ストレージのファイルバリデーションを指定
        }

        // カスタムバリデーションメッセージの定義
        $messages = [
            'product_image.required' => '機種画像をアップロードしてください。',
            'product_image.image' => '機種画像は有効な画像形式でなければなりません。',
            'product_name.required' => '商品名を入力してください。',
            'maker_name.required' => 'メーカー名を入力してください。',
            'category.required' => 'カテゴリーを入力してください。',
            'price.required' => '金額を入力してください。',
            'price.numeric' => '金額は数値で入力してください。',
            'release_date.required' => '発売日を入力してください。',
            'release_date.date' => '発売日は有効な日付形式で入力してください。',
            'internal_storage.required' => 'Pythonファイルをアップロードしてください。',
            'internal_storage.file' => 'Pythonファイルは有効なファイル形式でなければなりません。',
            'product_description.required' => '商品の説明を入力してください。',
        ];

        // アップロードされた画像ファイルを一時的に保存し、そのパスを取得
        $productImagePath = $request->hasFile('product_image')
            ? $request->file('product_image')->store('temp', 'public')
            : session('product_image_path');

        // 内部計算データ（Pythonファイル）も同様に保存
        $internalStoragePath = $request->hasFile('internal_storage')
            ? $request->file('internal_storage')->store('temp', 'private')
            : session('internal_storage_path');

        // 入力データをセッションに保存
        $request->session()->put([
            'product_image_path' => $productImagePath,
            'product_name' => $request->product_name,
            'maker_name' => $request->maker_name,
            'category' => $request->category,
            'price' => $request->price,
            'release_date' => $request->release_date,
            'internal_storage_path' => $internalStoragePath,
            'product_description' => $request->product_description,
            'is_published' => $request->is_published,
        ]);

        // 確認画面にリダイレクト
        return redirect()->route('product.edit.confirm', ['id' => $id]);
    }
    public function editConfirm($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.edit_confirm', compact('product'));
    }
    public function editProduct(Request $request, $id)
    {
        // 対象のプロダクトを取得
        $product = Product::findOrFail($id);

        // セッションからデータを取得
        $productName = $request->session()->get('product_name');
        $manufacturer = $request->session()->get('maker_name');
        $category = $request->session()->get('category');
        $price = $request->session()->get('price');
        $releaseDate = $request->session()->get('release_date');
        $description = $request->session()->get('product_description');
        $isPublished = $request->session()->get('is_published');

        // 画像と内部計算データのファイルのパス
        $productImagePath = $request->session()->get('product_image_path');
        $internalStoragePath = $request->session()->get('internal_storage_path');

        // セッションのデータを利用して商品のフィールドを更新
        if ($productName) {
            $product->name = $productName;
        }
        if ($manufacturer) {
            $product->manufacturer = $manufacturer;
        }
        if ($category) {
            $product->category = $category;
        }
        if ($price) {
            $product->price = $price;
        }
        if ($releaseDate) {
            $product->release_date = $releaseDate;
        }
        if ($description) {
            $product->description = $description;
        }

        $product->is_published = $isPublished !== null ? $isPublished : 0;

        if ($productImagePath) {
            $product->image_path = $productImagePath;
        }
        if ($internalStoragePath) {
            $product->python_file_path = $internalStoragePath;
        }

        // 更新データを保存
        $product->save();

        // セッションをクリア
        $request->session()->forget([
            'product_image_path',
            'product_name',
            'maker_name',
            'category',
            'price',
            'release_date',
            'internal_storage_path',
            'product_description',
            'is_published',
        ]);

        return redirect()->route('product.show', ['id' => $product->id])->with('success', '商品が更新されました。');
    }
}
