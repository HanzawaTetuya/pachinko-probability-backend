<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    // テーブル名を明示的に指定（デフォルトは複数形のテーブル名を推測するため）
    protected $table = 'purchases';

    // 一括代入可能なフィールド
    protected $fillable = ['user_id', 'order_id', 'product_id', 'license_id'];

    /**
     * ユーザーとのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'registration_number');
    }

    /**
     * 注文（Order）とのリレーション
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_number');
    }

    /**
     * 商品（Product）とのリレーション
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_number');
    }

    /**
     * ライセンス（License）とのリレーション
     */
    public function license()
    {
        return $this->belongsTo(License::class, 'license_id', 'license_key');
    }
}
