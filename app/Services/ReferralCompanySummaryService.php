<?php

namespace App\Services;

use App\Models\ReferralCompany;
use App\Models\ReferralCompanyMonthlySummary;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReferralCompanySummaryService
{
    public function generateCompanyMonthlySummaries()
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        // 今月の summary 一覧を取得（referral_code で紐付ける）
        $latestSummaries = ReferralCompanyMonthlySummary::where('year', $year)
            ->where('month', $month)
            ->get()
            ->keyBy('referral_code');

        // 今月の注文のうち、summary より新しいデータを持つ referral_code を抽出
        $updatedReferralCodes = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get()
            ->filter(function ($order) use ($latestSummaries) {
                $summary = $latestSummaries->get($order->referral_code);
                return !$summary || $order->created_at > $summary->updated_at;
            })
            ->pluck('referral_code')
            ->unique()
            ->values();

        if ($updatedReferralCodes->isEmpty()) {
            Log::info('【スキップ】注文データに更新はありません。月次報酬の再計算は不要です。');
            return;
        }

        // 更新が必要な企業だけ取得
        $companies = ReferralCompany::whereIn('referral_code', $updatedReferralCodes)->get();

        foreach ($companies as $company) {
            $referralCode = $company->referral_code;

            $ordersThisMonth = Order::where('referral_code', $referralCode)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->orderBy('created_at')
                ->get();

            if ($ordersThisMonth->isEmpty()) {
                continue;
            }

            $hasRewardCondition = $company->initial_reward_percentage > 0 && $company->initial_reward_times > 0;
            $onlyRecurring = !$hasRewardCondition && $company->recurring_reward_percentage > 0;

            $summary = ReferralCompanyMonthlySummary::updateOrCreate(
                [
                    'referral_code' => $referralCode,
                    'year' => $year,
                    'month' => $month,
                ],
                [
                    'total_orders' => $ordersThisMonth->count(),
                    'total_sales' => $ordersThisMonth->sum('total_price'),
                    'total_rewards' => 0,
                ]
            );

            $totalReward = 0;

            if ($hasRewardCondition) {
                Log::info("【報酬対象企業】企業名: {$company->company_name}, 紹介コード: {$referralCode}（初回から{$company->initial_reward_times}回目までが報酬対象／報酬率: {$company->initial_reward_percentage}%／継続報酬率: {$company->recurring_reward_percentage}%）");

                $loggedUserIds = [];

                foreach ($ordersThisMonth as $order) {
                    $userId = $order->user_id;

                    if (in_array($userId, $loggedUserIds)) {
                        continue;
                    }
                    $loggedUserIds[] = $userId;

                    $userOrders = Order::where('user_id', $userId)
                        ->orderBy('created_at')
                        ->get();

                    Log::info("　ユーザーID: {$userId} の注文履歴:");

                    $rewardEligibleOrders = $userOrders->take($company->initial_reward_times);

                    foreach ($userOrders as $uo) {
                        $isThisMonth = $uo->created_at->year === $year && $uo->created_at->month === $month;

                        if (!$isThisMonth) continue;

                        if ($rewardEligibleOrders->contains('order_number', $uo->order_number)) {
                            $rewardAmount = round($uo->total_price * ($company->initial_reward_percentage / 100), 2);
                            $totalReward += $rewardAmount;
                            Log::info("　　[報酬対象] 注文番号: {$uo->order_number}, 日時: {$uo->created_at}, 金額: {$uo->total_price}円, 報酬率: {$company->initial_reward_percentage}%, 報酬額: {$rewardAmount}円");
                        } elseif ($company->recurring_reward_percentage > 0) {
                            $rewardAmount = round($uo->total_price * ($company->recurring_reward_percentage / 100), 2);
                            $totalReward += $rewardAmount;
                            Log::info("　　[継続報酬対象] 注文番号: {$uo->order_number}, 日時: {$uo->created_at}, 金額: {$uo->total_price}円, 継続報酬率: {$company->recurring_reward_percentage}%, 報酬額: {$rewardAmount}円");
                        } else {
                            Log::info("　　[対象外] 注文番号: {$uo->order_number}, 日時: {$uo->created_at}, 報酬額: 0円（継続報酬なし）");
                        }
                    }
                }

                Log::info("【合計報酬額】企業名: {$company->company_name}（{$referralCode}）の今月の報酬合計: {$totalReward}円");
            } elseif ($onlyRecurring) {
                Log::info("【継続報酬のみ企業】企業名: {$company->company_name}, 紹介コード: {$referralCode}（継続報酬率: {$company->recurring_reward_percentage}%）");

                $loggedUserIds = [];

                foreach ($ordersThisMonth as $order) {
                    $userId = $order->user_id;

                    if (in_array($userId, $loggedUserIds)) {
                        continue;
                    }
                    $loggedUserIds[] = $userId;

                    $userOrders = Order::where('user_id', $userId)
                        ->orderBy('created_at')
                        ->get();

                    Log::info("　ユーザーID: {$userId} の注文履歴:");

                    foreach ($userOrders as $uo) {
                        $isThisMonth = $uo->created_at->year === $year && $uo->created_at->month === $month;

                        if ($isThisMonth) {
                            $rewardAmount = round($uo->total_price * ($company->recurring_reward_percentage / 100), 2);
                            $totalReward += $rewardAmount;
                            Log::info("　　[継続報酬対象] 注文番号: {$uo->order_number}, 日時: {$uo->created_at}, 金額: {$uo->total_price}円, 継続報酬率: {$company->recurring_reward_percentage}%, 報酬額: {$rewardAmount}円");
                        }
                    }
                }

                Log::info("【合計報酬額】企業名: {$company->company_name}（{$referralCode}）の今月の継続報酬合計: {$totalReward}円");
            }

            $summary->update([
                'total_rewards' => $totalReward,
            ]);
        }
    }
}
