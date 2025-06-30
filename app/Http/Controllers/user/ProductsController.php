<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Cart;


class ProductsController extends Controller
{
    // 商品一覧の呼び起こし
    public function showProducts()
    {
        // データベースから商品情報を取得
        $products = Product::all();

        // 必要なデータだけを返す
        return response()->json([
            'success' => true,
            'data' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'product_number' => $product->product_number,
                    'name' => $product->name,
                    'manufacturer' => $product->manufacturer,
                    'category' => $product->category,
                    'price' => (float) $product->price, // 数値型に変換
                    'release_date' => $product->release_date,
                    'description' => $product->description,
                ];
            }),
        ]);
    }
}
