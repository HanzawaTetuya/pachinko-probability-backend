<?php

namespace App\Http\Controllers\user;

use App\Models\Favorite;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    public function isFavorite(Request $request)
    {
        $request->validate([
            'product_number' => 'required|exists:products,product_number',
        ]);

        $user = Auth::user();
        $isFavorite = Favorite::where('user_registration_number', $user->registration_number)
            ->where('product_number', $request->product_number)
            ->exists();

        return response()->json(['is_favorite' => $isFavorite], 200);
    }

    public function favoriteStore(Request $request)
    {
        $request->validate([
            'product_number' => 'required|exists:products,product_number'
        ]);

        $user = Auth::user();
        $registrationNumber = $user->registration_number;

        $favorite = Favorite::firstOrCreate([
            'user_registration_number' => $registrationNumber,
            'product_number' => $request->product_number,
        ]);

        return response()->json([
            'message' => 'お気に入りに追加しました。',
            'favorite' => $favorite,
        ], 200);
    }

    public function favoriteDestroy(Request $request)
    {
        $request->validate([
            'product_number' => 'required|exists:products,product_number',
        ]);

        $deleted = Favorite::where('user_registration_number', Auth::user()->registration_number)
            ->where('product_number', $request->product_number)
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'お気に入りから削除しました。'], 200);
        }

        return response()->json(['message' => 'お気に入りに見つかりませんでした。'], 404);
    }

    public function favoriteIndex()
    {
        $userRegistrationNumber = Auth::user()->registration_number;

        $favorites = Favorite::with('product')
            ->where('user_registration_number', $userRegistrationNumber)
            ->get();

        $formatted = $favorites->map(function ($favorite) {
            $product = $favorite->product;
            return [
                'id' => $product->id,
                'product_number' => $product->product_number,
                'name' => $product->name,
                'manufacturer' => $product->manufacturer,
                'category' => $product->category,
                'price' => (float) $product->price,
                'release_date' => $product->release_date,
                'description' => $product->description,
            ];
        });

        return response()->json([
            'success' => true,
            'favorites' => $formatted,
        ]);
    }
}
