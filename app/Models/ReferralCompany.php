<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReferralCompany extends Model
{
    use HasFactory;

    protected $table = 'referral_companies';

    protected $fillable = [
        'company_name',
        'referral_code',
        'initial_reward_percentage',
        'initial_reward_times',
        'remaining_reward_times',
        'recurring_reward_percentage',
        'account_create_url',
    ];

    /**
     * 初回リワード回数を減算するメソッド
     */
    public function decrementRemainingReward()
    {
        if ($this->remaining_reward_times > 0) {
            $this->decrement('remaining_reward_times');
        }
    }

    /**
     * この紹介コードを使用したユーザー一覧
     */
    public function users()
    {
        return $this->hasMany(User::class, 'referral_code', 'referral_code');
    }

    /**
     * この紹介コードを使用した注文一覧
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'referral_code', 'referral_code');
    }

    protected $appends = ['reward', 'transactions', 'sales', 'rewards'];

    public function getRewardAttribute()
    {
        return $this->getRewardDescription();
    }

    public function getRewardDescription(): string
    {
        $initial = floatval($this->initial_reward_percentage);
        $times = intval($this->initial_reward_times);
        $recurring = floatval($this->recurring_reward_percentage);

        if ($initial > 0 && $times > 0 && $recurring > 0) {
            return $times === 1
                ? "初回決済のみ決済金額の{$initial}％を報酬とし、それ以降は決済金額の{$recurring}％を報酬とする"
                : "初回決済から{$times}回目まで決済金額の{$initial}％を報酬とし、それ以降は決済金額の{$recurring}％を報酬とする";
        }

        if ($initial > 0 && $times > 0) {
            return $times === 1
                ? "初回決済のみ決済金額の{$initial}％を報酬とする"
                : "初回決済から{$times}回目まで決済金額の{$initial}％を報酬とする";
        }

        if ($recurring > 0) {
            return "継続的に決済金額の{$recurring}％を報酬とする";
        }

        return "報酬なし";
    }

    public function currentMonthSummary()
    {
        $now = Carbon::now();
        return $this->hasOne(ReferralCompanyMonthlySummary::class, 'referral_code', 'referral_code')
            ->where('year', $now->year)
            ->where('month', $now->month);
    }

    public function getTransactionsAttribute()
    {
        return optional($this->currentMonthSummary)->total_orders ?? 0;
    }

    public function getSalesAttribute()
    {
        return optional($this->currentMonthSummary)->total_sales ?? 0;
    }

    public function getRewardsAttribute()
    {
        return optional($this->currentMonthSummary)->total_rewards ?? 0;
    }
}
