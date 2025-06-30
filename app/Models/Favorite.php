<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = ['user_registration_number', 'product_number'];

    // ユーザーリレーション
    public function user()
    {
        return $this->belongsTo(User::class, 'user_registration_number', 'registration_number');
    }

    // 商品リレーション
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_number', 'product_number');
    }
}
