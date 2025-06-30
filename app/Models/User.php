<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'registration_number',
        'name',
        'email',
        'date_of_birth',
        'password',
        'status',
        'referral_code',
    ];

    protected $guarded = [
        'registration_number',
    ];


    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime', // 日時としてキャスト
        'date_of_birth' => 'date',         // 日付型にキャスト
        'created_at' => 'datetime',        // 作成日時を日時型にキャスト
        'updated_at' => 'datetime',        // 更新日時を日時型にキャスト
        'status' => 'string',              // ステータスを文字列としてキャスト
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'user_registration_number', 'registration_number');
    }

    public function referralCompany()
    {
        return $this->belongsTo(ReferralCompany::class, 'referral_code', 'referral_code');
    }
}
