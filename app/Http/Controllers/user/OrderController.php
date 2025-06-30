<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function orderIndex()
    {
        try {
            $user = Auth::user();

            $orders = Order::where('user_id', $user->registration_number)
                ->where('status', 'success')
                ->get(['order_number', 'total_price']);

            return response()->json([
                'success' => true,
                'message' => '注文情報を取得しました。',
                'data' => $orders,
            ], 200);
        } catch (\Exception $e) {
            Log::error('注文情報取得エラー', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => '注文情報の取得中にエラーが発生しました。',
            ], 500);
        }
    }


    public function orderDetail(Request $request)
    {
        try {
            $orderNumber = $request->input('order_number');

            if (!$orderNumber) {
                return response()->json([
                    'success' => false,
                    'message' => '注文番号が見つかりません。',
                ], 400);
            }

            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => '指定された注文番号は存在しません。',
                ], 404);
            }

            $purchases = Purchase::where('order_id', $orderNumber)->get();

            if ($purchases->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => '購入情報が見つかりません。',
                ], 404);
            }

            $productsData = [];

            foreach ($purchases as $purchase) {
                $product = Product::where('product_number', $purchase->product_id)->first();

                if ($product) {
                    $productsData[] = [
                        'name' => $product->name,
                        'manufacturer' => $product->manufacturer,
                        'category' => $product->category,
                        'description' => $product->description,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'order_number' => $order->order_number,
                    'created_at' => $order->created_at,
                    'total_price' => $order->total_price,
                    'products' => $productsData,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('注文詳細取得エラー', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => '注文詳細の取得中にエラーが発生しました。',
            ], 500);
        }
    }
}
