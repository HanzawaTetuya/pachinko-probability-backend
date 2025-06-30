<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_registration_number', // ユーザーの登録番号
        'product_number',           // 商品番号
        'quantity',                 // 商品の数量
    ];

    /**
     * ユーザーとのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'registration_number', 'user_registration_number');
    }

    /**
     * 商品とのリレーション
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_number', 'product_number');
    }
}
