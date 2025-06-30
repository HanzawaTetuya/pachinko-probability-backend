<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCompanyMonthlySummary extends Model
{
    use HasFactory;

    protected $table = 'referral_company_monthly_summaries';

    protected $fillable = [
        'referral_code',
        'year',
        'month',
        'total_orders',
        'total_sales',
        'total_rewards',
    ];

    /**
     * 紐づく紹介企業情報
     */
    public function referralCompany()
    {
        return $this->belongsTo(ReferralCompany::class, 'referral_code', 'referral_code');
    }
}