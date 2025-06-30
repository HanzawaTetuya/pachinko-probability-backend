<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\SalesDaySummary;
use App\Models\SalesMonthSummary;
use App\Models\ReferralCompany;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Services\SalesSummaryService;
use App\Services\ReferralCompanySummaryService;

class SalesController extends Controller
{
    // 売上管理トップ閲覧
    public function showSalesPage(
        Request $request,
        SalesSummaryService $summaryService,
        ReferralCompanySummaryService $CompanySummaryService

    ) {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得

        // 売上取得
        $summaryService->generateDailySummary();
        $summaryService->generateMonthlySummary();

        // 日次処理
        $today = Carbon::today()->toDateString();
        $dailySales = SalesDaySummary::where('date', $today)->first();

        // 月次処理
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;
        $formattedMonth = $now->format('Y年m月');
        $monthlySales = SalesMonthSummary::where('year', $year)
            ->where('month', $month)
            ->first();

        // ReferralCompany
        $CompanySummaryService->generateCompanyMonthlySummaries();

        $companies = ReferralCompany::with(['currentMonthSummary'])->get();

        return view('admin.sales.show', compact(
            'admin',
            'dailySales',
            'today',
            'monthlySales',
            'formattedMonth',
            'companies'
        ));
    }

    // 提携企業登録
    public function showStorePage()
    {
        $admin = Auth::guard('admin')->user();  // ログイン中の管理者を取得

        return view('admin.sales.company_store', compact(
            'admin'
        ));
    }

    public function storeCompanyConfirm(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->all();

        // ✅ 重複チェック：同名の会社がすでに存在するか確認
        if (ReferralCompany::where('company_name', $data['company_name'])->exists()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'この企業名はすでに登録されています。');
        }

        // 数値化
        $initial = floatval($data['initial_reward_percentage'] ?? 0);
        $times = intval($data['initial_reward_times'] ?? 0);
        $recurring = floatval($data['recurring_reward_percentage'] ?? 0);

        // 報酬テキスト成型
        if ($initial > 0 && $times > 0 && $recurring > 0) {
            $data['reward'] = $times === 1
                ? "初回決済のみ決済金額の{$initial}％を報酬とし、それ以降は決済金額の{$recurring}％を報酬とする"
                : "初回決済から{$times}回目まで決済金額の{$initial}％を報酬とし、それ以降は決済金額の{$recurring}％を報酬とする";
        } elseif ($initial > 0 && $times > 0) {
            $data['reward'] = $times === 1
                ? "初回決済のみ決済金額の{$initial}％を報酬とする"
                : "初回決済から{$times}回目まで決済金額の{$initial}％を報酬とする";
        } elseif ($recurring > 0) {
            $data['reward'] = "継続的に決済金額の{$recurring}％を報酬とする";
        } else {
            $data['reward'] = "報酬なし";
        }

        Log::info('【登録データ】', $data);

        return view('admin.sales.company_store_confirm', compact('admin', 'data'));
    }

    public function storeCompany(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->all();
        Log::info('【登録データ】', $data);

        // 10桁のランダムな数字で referral_code を生成
        $referralCode = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);

        // account_create_url の生成
        $accountCreateUrl = 'https://www.sigma-alpha.jp/create?==ad' . $referralCode;

        // データベースに登録
        ReferralCompany::create([
            'company_name' => $data['company_name'],
            'initial_reward_percentage' => $data['initial_reward_percentage'],
            'initial_reward_times' => $data['initial_reward_times'],
            'remaining_reward_times' => $data['initial_reward_times'],
            'recurring_reward_percentage' => $data['recurring_reward_percentage'],
            'referral_code' => $referralCode,
            'account_create_url' => $accountCreateUrl,
        ]);

        return redirect()->route('show.sales.index')->with('success', '提携先企業を登録しました。');
    }

    // 日次売り上げ管理
    public function showDailySales()
    {

        Log::info('【✅ showDailySales 実行】呼び出されました');
        $admin = Auth::guard('admin')->user();

        $today = Carbon::today()->toDateString();
        $dailySales = SalesDaySummary::where('date', $today)->first();

        // 🔍 日次売上をログ出力
        if ($dailySales) {
            Log::info('【本日の日次売上データ】', $dailySales->toArray());
        } else {
            Log::info('【本日の日次売上データ】該当データが存在しません');
        }

        $orders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.registration_number')
            ->select(
                'orders.order_number',
                'orders.user_id',
                'users.name as user_name',
                'orders.total_price',
                'orders.referral_code',
                DB::raw('DATE(orders.created_at) as order_date')
            )
            ->whereDate('orders.created_at', $today) // ← 当日のみ
            ->orderBy('orders.created_at', 'desc')
            ->paginate(10);


        session(['previous_page' => 'daily']);

        return view('admin.sales.days', compact('admin', 'orders', 'today', 'dailySales'));
    }

    // 月次売り上げ管理
    public function showMonthlySales()
    {
        $admin = Auth::guard('admin')->user();

        // 現在の年月を取得
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;
        $formattedMonth = $now->format('Y年m月');

        // 月次売上サマリー取得
        $monthlySales = SalesMonthSummary::where('year', $year)
            ->where('month', $month)
            ->first();

        // 🔍 日次売上をログ出力
        if ($monthlySales) {
            Log::info('【本日の日次売上データ】', $monthlySales->toArray());
        } else {
            Log::info('【本日の日次売上データ】該当データが存在しません');
        }

        $orders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.registration_number')
            ->select(
                'orders.order_number',
                'orders.user_id',
                'users.name as user_name',
                'orders.total_price',
                'orders.referral_code',
                DB::raw('DATE(orders.created_at) as order_date')
            )
            ->whereYear('orders.created_at', $year)
            ->whereMonth('orders.created_at', $month)
            ->orderBy('orders.created_at', 'desc')
            ->paginate(10);


        session(['previous_page' => 'monthly']);

        return view('admin.sales.month', compact('admin', 'formattedMonth', 'monthlySales', 'orders'));
    }

    // 売上詳細取得
    public function showSalesDetail(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $orderNumber = $request->input('order_number');
        $previous = $request->input('previous', 'monthly'); // デフォルトは monthly
        Log::info("【注文詳細】受け取った previous パラメータ:", ['previous' => $previous]);


        // 注文情報（1件）
        $order = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.registration_number')
            ->leftJoin('referral_companies', 'orders.referral_code', '=', 'referral_companies.referral_code')
            ->select(
                'orders.order_number',
                'orders.user_id',
                'users.name as user_name',
                'orders.total_price',
                'orders.referral_code',
                DB::raw('DATE(orders.created_at) as order_date')
            )
            ->where('orders.order_number', $orderNumber)
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', '注文が見つかりませんでした。');
        }

        // 購入商品情報（複数件）
        $purchasedItems = DB::table('purchases')
            ->join('products', 'purchases.product_id', '=', 'products.product_number')
            ->select('products.name as product_name', 'purchases.license_id')
            ->where('purchases.order_id', $orderNumber)
            ->get();

        // ログ出力
        Log::info('【注文詳細データ】', (array) $order);
        Log::info('【購入商品リスト】', $purchasedItems->toArray());

        return view('admin.sales.sales_detail', compact('admin', 'order', 'purchasedItems', 'previous'));
    }


    // 企業詳細
    public function showCompanyDetail($referralCode)
    {
        $admin = Auth::guard('admin')->user();

        // 紹介企業を referral_code で取得
        $company = ReferralCompany::where('referral_code', $referralCode)->firstOrFail();
        Log::info('【企業詳細表示】紹介企業情報:', $company->toArray());


        // この企業に紐づく注文一覧を取得（新しい順）
        $orders = Order::where('referral_code', $referralCode)
            ->orderBy('created_at', 'desc')
            ->get();

        Log::info("【企業詳細表示】注文一覧（referral_code: {$referralCode}）:", $orders->toArray());


        return view('admin.sales.company_detail_show', compact('admin', 'company', 'orders'));
    }


    // 企業報酬額変更
    public function editCompany(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $referralCode = $request->input('referral_code');

        $company = DB::table('referral_companies')
            ->where('referral_code', $referralCode)
            ->first();

        if (!$company) {
            Log::warning("【企業情報なし】referral_code: {$referralCode}");
            return redirect()->back()->with('error', '企業情報が見つかりませんでした。');
        }

        // 報酬情報ログ
        Log::info("【報酬情報取得】referral_code: {$referralCode}", [
            'initial_reward_percentage' => $company->initial_reward_percentage,
            'initial_reward_times' => $company->initial_reward_times,
            'recurring_reward_percentage' => $company->recurring_reward_percentage,
        ]);

        Log::info('【編集対象企業の全データ】', (array) $company);

        return view('admin.sales.company_edit', compact('admin', 'company'));
    }

    public function editCompanyConfirm(Request $request)
    {
        $validated = $request->validate([
            'referral_code' => 'required|string',
            'initial_reward_percentage' => 'required|numeric|min:0',
            'initial_reward_times' => 'required|integer|min:0',
            'recurring_reward_percentage' => 'required|numeric|min:0',
        ]);

        DB::table('referral_companies')
            ->where('referral_code', $validated['referral_code'])
            ->update([
                'initial_reward_percentage' => $validated['initial_reward_percentage'],
                'initial_reward_times' => $validated['initial_reward_times'],
                'recurring_reward_percentage' => $validated['recurring_reward_percentage'],
                'updated_at' => now(),
            ]);

        Log::info('【報酬情報更新】', $validated);

        return redirect()->route('show.monthly.sales')->with('success', '報酬情報を更新しました。');
    }
}
