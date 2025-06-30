<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use App\Models\License;
use App\Models\Purchase;
use App\Models\User;

class DebugPaymentController extends Controller
{
    public function handleResult(Request $request)
    {
        try {
            $orderNumber = $request->input('cod');
            $amount = intval($request->input('am'));
            $result = $request->input('rst', '1');

            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                return response('NG', 200);
            }

            if ($amount !== intval($order->total_price)) {
                return response('NG', 200);
            }

            $userId = $order->user_id;

            if ($result === '1' && $order->status === 'pending') {
                $order->status = 'success';
                $order->save();

                $cartItems = Cart::where('user_registration_number', $userId)->get();
                $productNumbers = $cartItems->pluck('product_number')->toArray();
                $products = Product::whereIn('product_number', $productNumbers)->get();

                foreach ($cartItems as $item) {
                    $licenseKey = hash('sha256', $userId . $item->product_number . uniqid(mt_rand(), true));

                    License::create([
                        'user_id' => $userId,
                        'product_id' => $item->product_number,
                        'license_key' => $licenseKey,
                    ]);

                    Purchase::create([
                        'user_id' => $userId,
                        'order_id' => $orderNumber,
                        'product_id' => $item->product_number,
                        'license_id' => $licenseKey,
                    ]);
                }

                Cart::where('user_registration_number', $userId)->delete();

                $recommendedProduct = Product::orderBy('release_date', 'desc')->first();

                return view('user.thanksPage', [
                    'orderNumber' => $orderNumber,
                    'amount' => $amount,
                    'products' => $products,
                    'recommendedProduct' => $recommendedProduct,
                ]);
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            return response('NG', 200);
        }
    }
}
