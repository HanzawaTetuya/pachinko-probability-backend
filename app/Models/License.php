<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    // テーブル名を明示的に指定（デフォルトは複数形のテーブル名を推測）
    protected $table = 'licenses';

    // 一括代入可能なフィールド
    protected $fillable = ['user_id', 'product_id', 'license_key'];

    /**
     * ユーザーとのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'registration_number');
    }

    /**
     * 商品（Product）とのリレーション
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_number');
    }
}

