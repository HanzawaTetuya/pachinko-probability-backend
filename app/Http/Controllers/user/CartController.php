<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Purchase;
use Illuminate\Support\Facades\Log;


class CartController extends Controller
{

    public function cartIndex()
    {
        $userRegistrationNumber = Auth::user()->registration_number;

        $carts = Cart::with('product')
            ->where('user_registration_number', $userRegistrationNumber)
            ->get();

        $responseData = [
            'success' => true,
            'carts' => $carts->map(function ($cart) {
                return [
                    'id' => $cart->product->id,
                    'product_number' => $cart->product->product_number,
                    'name' => $cart->product->name,
                    'manufacturer' => $cart->product->manufacturer,
                    'category' => $cart->product->category,
                    'price' => (float) $cart->product->price,
                    'release_date' => $cart->product->release_date,
                    'description' => $cart->product->description,
                ];
            }),
        ];

        return response()->json($responseData);
    }


    public function isInCart(Request $request)
    {
        $request->validate([
            'product_number' => 'required|exists:products,product_number',
        ]);

        $user = Auth::user();
        $isInCart = Cart::where('user_registration_number', $user->registration_number)
            ->where('product_number', $request->product_number)
            ->exists();

        return response()->json(['is_in_cart' => (bool) $isInCart], 200);
    }


    public function addToCart(Request $request)
    {
        $request->validate([
            'product_number' => 'required|exists:products,product_number',
        ]);

        $userRegistrationNumber = Auth::user()->registration_number;

        if (Cart::where('user_registration_number', $userRegistrationNumber)
            ->where('product_number', $request->product_number)
            ->exists()
        ) {
            return response()->json([
                'status' => 'already_in_cart',
                'message' => '既にカートに追加されています。',
            ], 400);
        }

        if (Purchase::where('user_id', $userRegistrationNumber)
            ->where('product_id', $request->product_number)
            ->exists()
        ) {
            return response()->json([
                'status' => 'already_purchased',
                'message' => '既に購入済みです。',
            ], 400);
        }

        Cart::create([
            'user_registration_number' => $userRegistrationNumber,
            'product_number' => $request->product_number,
            'quantity' => 1,
        ]);

        return response()->json([
            'status' => 'added_to_cart',
            'success' => true,
            'message' => 'カートに追加しました。',
        ], 200);
    }

    public function cartDestroy($product_number)
    {
        $userRegNo = Auth::user()->registration_number;

        $deleted = Cart::where('user_registration_number', $userRegNo)
            ->where('product_number', $product_number)
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'カートから削除しました。'], 200);
        }

        return response()->json(['message' => 'カート内に商品が見つかりませんでした。'], 404);
    }
}
