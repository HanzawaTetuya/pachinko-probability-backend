<?php

namespace App\Http\Controllers\user\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\License;
use Illuminate\Support\Facades\Http;

class RobotPaymentController extends Controller
{
    public function confirm(Request $request)
    {
        // ★ 必須：リクエストから送るデータを取り出す
        $data = $request->only([
            'tkn','cvv','aid','am','tx',
            'sf','em','cod','fn','ln','jb','rt'
        ]);

        try {
            $response = Http::asForm()->post('https://credit.j-payment.co.jp/gateway/gateway_token.aspx', $data);

            if (str_contains($response->body(), 'Success')) {
                return redirect()->route('payment.thanks');
            }

            return redirect()->back()->with('error', '決済失敗：' . $response->body());
        } catch (\Exception $e) {
            Log::error('RobotPayment 通信エラー', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', '通信エラー：' . $e->getMessage());
        }
    }


    public function handleResult(Request $request)
    {
        try {
            Log::info('【RobotPayment キックバック受信】', $request->all());

            $orderNumber = $request->input('cod');
            $amount = intval($request->input('am'));
            $result = $request->input('rst');

            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                Log::warning('注文番号が見つかりません', ['cod' => $orderNumber]);
                return response('NG', 200);
            }

            if ($amount !== intval($order->total_price)) {
                Log::warning('金額改ざんの可能性', [
                    'order_number' => $orderNumber,
                    '送信金額' => $amount,
                    '注文金額' => $order->total_price
                ]);
                return response('NG', 200);
            }

            $userId = $order->user_id;

            if ($result === '1' && $order->status === 'pending') {
                $order->status = 'success';
                $order->save();

                Log::info('【決済成功】ステータス更新', [
                    'order_number' => $orderNumber,
                    'status' => 'success'
                ]);

                $cartItems = Cart::where('user_registration_number', $userId)->get();
                $productNumbers = $cartItems->pluck('product_number')->toArray();
                $products = Product::whereIn('product_number', $productNumbers)->get();

                foreach ($cartItems as $item) {
                    $licenseKey = hash('sha256', $userId . $item->product_number . uniqid(mt_rand(), true));

                    $license = License::create([
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

                $recommendedProduct = Product::whereIn('product_number', $productNumbers)
                    ->orderBy('release_date', 'desc')
                    ->first();

                return view('user.thanksPage', [
                    'order_number' => $orderNumber,
                    'amount' => $amount,
                    'products' => $products,
                    'recommendedProduct' => $recommendedProduct,
                ]);
            }

            // ❗️ すでに処理済み or 異常な result
            Log::info('処理不要もしくは不正 result のためスキップ', [
                'order_number' => $orderNumber,
                'result' => $result,
                'status' => $order->status,
            ]);

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('決済キックバック処理中にエラー', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response('NG', 200); // エラーでもRobotPaymentには 200 OK を返す方が無難なことも多い
        }
    }
}
