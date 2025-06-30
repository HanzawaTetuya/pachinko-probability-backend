<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\ResultUsage;
use App\Models\License;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    // 決済フォームの作成処理スタート
    public function startPurchase(Request $request)
    {
        $user = Auth::user();
        Log::info('User fetched', ['user_id' => $user->registration_number]);

        $userId = $user->registration_number;
        $referralCode = $user->referral_code ?? null;

        $cartItems = Cart::where('user_registration_number', $userId)->get();
        Log::info('Cart items fetched', ['cart_items' => $cartItems]);

        $productNumbers = $cartItems->pluck('product_number')->toArray();

        $products = Product::whereIn('product_number', $productNumbers)
            ->select('product_number', 'name', 'manufacturer', 'category', 'price')
            ->get();
        Log::info('Products fetched', ['products' => $products]);

        $totalPrice = (int) $products->sum('price');
        Log::info('Total price calculated', ['total_price' => $totalPrice]);

        $orderNumber = str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
        Log::info('Order number generated', ['order_number' => $orderNumber]);

        $order = [
            'user_id' => $userId,
            'order_number' => $orderNumber,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'referral_code' => $referralCode,
        ];

        $orderModel = Order::create($order);
        Log::info('Order created', ['order_id' => $orderModel->id]);

        $token = $request->bearerToken();
        $viewUrl = url('/purchase/checkout') . '?order_number=' . $orderModel->order_number . '&token=' . $token;
        Log::info('Payment URL generated', ['payment_url' => $viewUrl]);

        return response()->json([
            'payment_url' => $viewUrl,
        ]);
    }
    public function showCheckoutPage(Request $request)
    {
        try {
            $token = $request->query('token');

            if ($token) {
                $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($accessToken) {
                    Auth::login($accessToken->tokenable);
                    Log::info('ユーザー認証成功', ['user_id' => Auth::id()]);
                } else {
                    Log::warning('無効なアクセストークン');
                }
            } else {
                Log::warning('URLにトークンが含まれていません');
            }

            if (!Auth::check()) {
                Log::warning('認証失敗：ログインしていません');
                abort(401, 'Unauthorized');
            }

            $orderNumber = $request->query('order_number');

            if (!$orderNumber) {
                Log::warning('注文番号がリクエストに含まれていません');
                abort(400, '注文番号が必要です');
            }

            $order = Order::where('order_number', $orderNumber)->firstOrFail();

            $productNumbers = Cart::where('user_registration_number', $order->user_id)
                ->pluck('product_number')
                ->toArray();

            $products = Product::whereIn('product_number', $productNumbers)->get();

            // ⭐️ 追加！注文ユーザー情報も取得する
            $user = User::where('registration_number', $order->user_id)->first();

            return view('user.checkout', [
                'order' => $order,
                'products' => $products,
                'email' => $user->email, // 🔥 emailだけ渡す
            ]);
        } catch (\Exception $e) {
            Log::error('チェックアウトページの表示中に例外が発生しました', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, 'システムエラーが発生しました');
        }
    }


    // 決済フォームの作成処理エンド
}
