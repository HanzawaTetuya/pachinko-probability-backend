<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ResultUsage;
use App\Models\Product;
use App\Models\News;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class HomePageController extends Controller
{
    public function homeData()
    {
        try {
            // ########## ログイン中のユーザー情報の取得 ##########
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'ユーザーがログインしていません。',
                ], 401);
            }

            // ########## 使用履歴の取得 ##########
            $userId = $user->registration_number;
            $usageDate = date('Y-m-d');

            $usageData = ResultUsage::where('user_id', $userId)
                ->where('usage_date', $usageDate)
                ->select('result_number', 'usage_date', 'usage_count')
                ->first();

            // ########## 商品の取得 ##########
            $product = Product::orderBy('release_date', 'desc')->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => '商品データが存在しません。',
                ], 200);
            }

            // ########## 最新ニュースの取得 ##########
            $newsList = News::with(['tags:id,name'])
                ->select('id', 'title', 'content', 'image_path', 'published_at')
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();

            // ########## 必要なデータをまとめて返却 ##########
            return response()->json([
                'success' => true,
                'message' => 'データを正常に取得しました。',
                'userInfo' => [
                    'user_id' => $user->registration_number,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'usageData' => $usageData ? [
                    'result_number' => $usageData->result_number,
                    'usage_date' => $usageData->usage_date,
                    'usage_count' => $usageData->usage_count,
                ] : null,
                'product' => [
                    'id' => $product->id,
                    'product_number' => $product->product_number,
                    'name' => $product->name,
                    'manufacturer' => $product->manufacturer,
                    'category' => $product->category,
                    'price' => (float) $product->price,
                    'release_date' => $product->release_date,
                    'description' => $product->description,
                ],
                'newsData' => $newsList,
            ], 200);
        } catch (\Exception $e) {
            // 最低限のエラーログのみ残す（ユーザーには詳細は返さない）
            Log::error('homeData メソッドエラー: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'データ取得中にエラーが発生しました。',
            ], 500);
        }
    }
}
