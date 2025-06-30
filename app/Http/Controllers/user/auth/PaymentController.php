<?php

namespace App\Http\Controllers\user\auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\ResultUsage;
use App\Models\License;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function generatePaymentLink()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => '認証されていません。'], 401);
            }

            $userId = $user->registration_number;
            $referralCode = $user->referral_code ?? null;

            $cart = Cart::where('user_registration_number', $userId)->get();
            if ($cart->isEmpty()) {
                return response()->json(['error' => 'カートが空です。'], 400);
            }

            $productNumbers = $cart->pluck('product_number');
            $products = Product::whereIn('product_number', $productNumbers)->get();

            if ($products->isEmpty()) {
                return response()->json(['error' => '商品が見つかりません。'], 400);
            }

            $totalPrice = $products->sum('price');
            $orderNumber = str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);

            Order::create([
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'total_price' => $totalPrice,
                'status' => 'success',
                'referral_code' => $referralCode,
            ]);

            return response()->json(['order_number' => $orderNumber]);
        } catch (\Exception $e) {
            return response()->json(['error' => '決済処理に失敗しました。'], 500);
        }
    }

    public function checkOrderStatus(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|exists:orders,order_number',
        ]);

        $order = Order::where('order_number', $request->order_number)->first();

        if (!$order) {
            return response()->json([
                'status' => 'not_found',
                'message' => '注文が見つかりません。',
            ], 404);
        }

        if ($order->status === 'pending') {
            return response()->json([
                'status' => 'pending',
                'message' => '注文は処理中です。',
            ], 200);
        }

        if ($order->status === 'success') {
            $userId = $order->user_id;

            if (!Purchase::where('order_id', $order->order_number)->exists()) {
                $cartItems = Cart::where('user_registration_number', $userId)->get();

                foreach ($cartItems as $item) {
                    $licenseKey = Str::uuid()->toString();

                    License::create([
                        'user_id' => $userId,
                        'product_id' => $item->product_number,
                        'license_key' => $licenseKey,
                    ]);

                    Purchase::create([
                        'user_id' => $userId,
                        'order_id' => $order->order_number,
                        'product_id' => $item->product_number,
                        'license_id' => $licenseKey,
                    ]);
                }

                Cart::where('user_registration_number', $userId)->delete();
            }

            return response()->json([
                'status' => 'success',
                'order_number' => $order->order_number,
                'message' => '注文が完了しました。',
            ], 200);
        }

        return response()->json([
            'status' => 'unknown_status',
            'message' => '不明なステータスです。',
        ], 400);
    }

    public function getOrder(Request $request)
    {
        $request->validate([
            'order_number' => 'required',
        ]);

        $orderNumbers = is_array($request->order_number)
            ? $request->order_number
            : [$request->order_number];

        try {
            $orders = Order::whereIn('order_number', $orderNumbers)->get();

            if ($orders->isEmpty()) {
                return response()->json(['error' => '注文が見つかりません。'], 404);
            }

            $totalPrice = $orders->sum('total_price');

            $purchases = Purchase::whereIn('order_id', $orders->pluck('order_number'))
                ->with('product')
                ->get();

            $products = $purchases->map(function ($purchase) {
                return [
                    'product_id' => $purchase->product_id,
                    'product_name' => $purchase->product->name ?? '不明',
                ];
            });

            return response()->json([
                'order_number' => $orderNumbers,
                'total_price' => $totalPrice,
                'products' => $products,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => '注文データの取得中にエラーが発生しました。'], 500);
        }
    }

    public function getOrderAll()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '認証されていません。',
                ], 401);
            }

            $userId = $user->registration_number;
            $todayDate = now()->toDateString();

            $purchases = Purchase::where('user_id', $userId)
                ->select('product_id', 'license_id', 'created_at')
                ->with(['product' => function ($query) {
                    $query->select('product_number', 'name');
                }])
                ->get();

            $resultUsage = ResultUsage::where('user_id', $userId)
                ->where('usage_date', $todayDate)
                ->first();

            $data = $purchases->map(function ($purchase) {
                return [
                    'product_number' => $purchase->product->product_number ?? null,
                    'name' => $purchase->product->name ?? '不明',
                    'license_id' => $purchase->license_id,
                    'created_at' => $purchase->created_at->format('Y-m-d H:i:s'),
                ];
            });

            $resultUsageData = [
                'usage_date' => $resultUsage ? $resultUsage->created_at : 'データがありません',
                'usage_count' => $resultUsage ? $resultUsage->usage_count : 0,
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'result_usage' => $resultUsageData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '購入情報の取得中にエラーが発生しました。',
            ], 500);
        }
    }
}
