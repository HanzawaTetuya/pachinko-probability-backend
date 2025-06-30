<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders'; // テーブル名を明示

    protected $fillable = [
        'user_id',
        'order_number',
        'total_price',
        'status',
        'referral_code',
    ];

    /**
     * ユーザーとのリレーション（注文者）
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'registration_number');
    }

    /**
     * 購入データとのリレーション（注文に紐づく購入情報）
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'order_id', 'order_number');
    }

    /**
     * 紹介企業とのリレーション（注文を紹介した企業）
     */
    public function referralCompany()
    {
        return $this->belongsTo(ReferralCompany::class, 'referral_code', 'referral_code');
    }
}
