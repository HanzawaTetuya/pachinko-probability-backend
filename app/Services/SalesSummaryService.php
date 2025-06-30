<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SalesDaySummary;
use App\Models\SalesMonthSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesSummaryService
{
    public function generateDailySummary()
    {

        //日別なので、一旦すべて集計する方式はずらしません。 
        $today = Carbon::today()->toDateString();
        // Log::info('【日次集計処理】今日の日付：' . $today);

        // summary データの取得
        $existingSummary = SalesDaySummary::where('date', $today)->first();
        $summaryUpdatedAt = $existingSummary?->updated_at;
        // Log::info('【日次集計処理】既存 summary の更新日時：' . ($summaryUpdatedAt ?? 'なし'));

        // order テーブルで今日の注文の最新作成日時を取得
        $latestOrderCreatedAt = Order::whereDate('created_at', $today)
            ->orderByDesc('created_at')
            ->value('created_at');

        // Log::info('【日次集計処理】最新の order 作成日時：' . ($latestOrderCreatedAt ?? 'なし'));

        // ▼ summary がない or 最新注文の方が新しい場合 → 再計算 & 保存
        if (!$existingSummary || ($latestOrderCreatedAt && $summaryUpdatedAt < $latestOrderCreatedAt)) {
            $orders = Order::whereDate('created_at', $today)->get();
            $totalOrders = $orders->count();
            $totalSales = $orders->sum('total_price');

            // Log::info('【日次集計処理】件数：' . $totalOrders);
            // Log::info('【日次集計処理】合計売上：' . $totalSales);

            SalesDaySummary::updateOrCreate(
                ['date' => $today],
                [
                    'total_sales' => $totalSales,
                    'total_orders' => $totalOrders,
                ]
            );

            Log::info('【日次集計処理】sales_day_summaries に保存完了');
        } else {
            Log::info('【日次集計処理】更新不要のためスキップ');
        }
    }

    public function generateMonthlySummary()
    {

        // Log::info('【月次集計処理】>>> 呼び出された', debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3));

        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        // Log::info("【月次集計処理】対象年月：{$year}年{$month}月");

        // summary データの取得
        $existingSummary = SalesMonthSummary::where('year', $year)->where('month', $month)->first();
        $summaryUpdatedAt = $existingSummary?->updated_at;
        // Log::info('【月次集計処理】既存 summary の更新日時：' . ($summaryUpdatedAt ?? 'なし'));

        // order テーブルで今月の注文の最新作成日時を取得
        $latestOrderCreatedAt = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderByDesc('created_at')
            ->value('created_at');

        // Log::info('【月次集計処理】最新の order 作成日時：' . ($latestOrderCreatedAt ?? 'なし'));

        // ▼ summary がない or 最新注文の方が新しい場合 → 再計算 & 保存
        if (!$existingSummary || ($latestOrderCreatedAt && $summaryUpdatedAt < $latestOrderCreatedAt)) {
            $orders = Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get();

            $totalOrders = $orders->count();
            $totalSales = $orders->sum('total_price');

            // Log::info("【月次集計処理】件数：{$totalOrders}");
            // Log::info("【月次集計処理】合計売上：{$totalSales}");

            SalesMonthSummary::updateOrCreate(
                ['year' => $year, 'month' => $month],
                [
                    'total_sales' => $totalSales,
                    'total_orders' => $totalOrders,
                ]
            );

            Log::info('【月次集計処理】sales_month_summaries に保存完了');
        } else {
            Log::info('【月次集計処理】更新不要のためスキップ');
        }
    }
}
