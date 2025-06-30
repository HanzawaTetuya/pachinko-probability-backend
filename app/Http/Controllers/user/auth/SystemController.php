<?php

namespace App\Http\Controllers\user\auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\License;
use App\Models\Product;
use App\Models\Result;
use App\Models\ResultUsage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Carbon\Carbon;

class SystemController extends Controller
{

    public function verifyLicense(Request $request)
    {
        $request->validate([
            'product_number' => 'required|integer|exists:products,product_number',
            'license_id' => 'required|string|max:255',
        ]);

        try {
            $userId = Auth::user()->registration_number;

            $license = License::where('user_id', $userId)
                ->where('product_id', $request->product_number)
                ->first();

            if (!$license) {
                return response()->json([
                    'success' => false,
                    'message' => 'ライセンスを所有していません。',
                ], 403);
            }

            $product = Product::where('product_number', $request->product_number)->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => '商品が見つかりません。',
                ], 404);
            }

            if (hash_equals($license->license_key, $request->license_id)) {
                return response()->json([
                    'success' => true,
                    'message' => 'ライセンスの検証に成功しました。',
                    'license_id' => $license->license_key,
                    'product_id' => $request->product_number,
                    'name' => $product->name,
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'ライセンスが一致しません。',
            ], 403);
        } catch (\Exception $e) {
            Log::error('ライセンス検証中に例外が発生しました。', [
                'user_id' => Auth::user()->registration_number ?? null,
                'product_number' => $request->product_number ?? null,
                'provided_license_id' => $request->license_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => '検証エラーが発生しました。',
            ], 500);
        }
    }


    public function calculate(Request $request)
    {
        try {
            $user = Auth::user();
            $registrationNumber = $user->registration_number;
            $todayDate = now()->toDateString();

            $existingUsage = ResultUsage::where('user_id', $registrationNumber)
                ->where('usage_date', $todayDate)
                ->first();

            if ($existingUsage && $existingUsage->usage_count >= 30) {
                return response()->json([
                    'success' => false,
                    'message' => '本日の使用回数が上限に達しました。',
                ], 403);
            }

            $product = Product::where('product_number', $request->input('product_number'))->first();
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => '該当する商品が見つかりません。',
                ], 404);
            }

            $pythonFilePath = str_replace(['//', '\\'], DIRECTORY_SEPARATOR, $product->getPythonFile());

            if (!file_exists($pythonFilePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pythonスクリプトが存在しません。',
                ], 500);
            }

            // $command = [
            //     'C:\\Users\\metor\\AppData\\Local\\Programs\\Python\\Python313\\python.exe',
            //     $pythonFilePath,
            //     $request->input('rotation'),
            //     $request->input('initial_hits'),
            //     $request->input('total_hits'),
            // ];

            $command = [
                '/usr/bin/python3',
                $pythonFilePath,
                $request->input('rotation'),
                $request->input('initial_hits'),
                $request->input('total_hits'),
            ];

            $process = new Process($command);
            $process->run();

            if (!$process->isSuccessful()) {
                return response()->json([
                    'success' => false,
                    'message' => '計算範囲対象外の数値が入力されました。',
                ], 500);
            }

            $output = $process->getOutput();
            $result = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pythonスクリプトの出力が無効です。',
                ], 500);
            }

            $hitProbability = $result['adjusted_probability_100'] ?? null;
            $chainProbability = $result['prob_more_than_adjusted'] ?? null;

            if (is_null($hitProbability) || is_null($chainProbability)) {
                return response()->json([
                    'success' => false,
                    'message' => '必要なデータが不足しています。',
                ], 422);
            }

            if ($hitProbability <= 0.1 || $hitProbability >= 100 || $chainProbability <= 0.1 || $chainProbability >= 100) {
                return response()->json([
                    'success' => false,
                    'message' => '期待値がほぼ0の台です。',
                ], 422);
            }

            $resultNumber = $existingUsage
                ? $existingUsage->result_number
                : $registrationNumber . now()->format('Ymd') . mt_rand(1000, 9999);

            if ($existingUsage) {
                $usageCount = Result::where('result_number', $resultNumber)->count();
                $existingUsage->update(['usage_count' => $usageCount]);
            } else {
                $resultUsage = ResultUsage::create([
                    'user_id' => $registrationNumber,
                    'result_number' => $resultNumber,
                    'usage_date' => $todayDate,
                    'usage_count' => 1,
                ]);
            }

            $dbResult = [
                'result_number' => $resultNumber,
                'machine_number' => $request->input('machine_number'),
                'product_name' => $request->input('product_name'),
                'hit_probability' => $result['adjusted_probability_100'],
                'expected_chain_count' => $result['adjusted_chain_expectation'],
                'cash_balance_3_3' => $result['adjusted_profit_range'],
                'chain_probability' => $result['prob_more_than_adjusted'],
                'current_bonus' => $result['range_result'],
            ];

            Result::create($dbResult);

            ResultUsage::where('result_number', $resultNumber)
                ->update(['usage_count' => Result::where('result_number', $resultNumber)->count()]);

            return response()->json([
                'success' => true,
                'data' => [
                    'adjusted_probability_100' => $result['adjusted_probability_100'],
                    'adjusted_chain_expectation' => $result['adjusted_chain_expectation'],
                    'adjusted_profit_range' => $result['adjusted_profit_range'],
                    'prob_more_than_adjusted' => $result['prob_more_than_adjusted'],
                    'range_result' => $result['range_result'],
                    'usage_date' => $existingUsage->created_at ?? $resultUsage->created_at ?? now(),
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('システムエラー', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'システムエラーが発生しました。',
            ], 500);
        }
    }



    public function fetchUsageData(Request $request)
    {
        try {
            $usageDate = now()->toDateString();

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '認証情報が見つかりません。',
                ], 401);
            }

            $registrationNumber = $user->registration_number;

            $resultUsage = ResultUsage::where('user_id', $registrationNumber)
                ->where('usage_date', $usageDate)
                ->first();

            if (!$resultUsage) {
                return response()->json([
                    'success' => false,
                    'message' => '指定日の利用データが見つかりません。',
                ], 404);
            }

            $resultNumber = $resultUsage->result_number;

            $results = Result::where('result_number', $resultNumber)
                ->select('id', 'product_name', 'hit_probability', 'cash_balance_3_3', 'machine_number')
                ->get();

            if ($results->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => '結果データが見つかりません。',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'データ取得に成功しました。',
                'result_usage' => $resultUsage,
                'results' => $results,
            ], 200);
        } catch (\Exception $e) {
            Log::error('fetchUsageData 例外', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'データ取得中にエラーが発生しました。',
            ], 500);
        }
    }


    public function getDataDetail(Request $request)
    {
        try {
            $resultNumber = $request->input('result_number');
            $id = $request->input('id');

            if (empty($resultNumber) || empty($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'result_number と id は必須です。',
                ], 400);
            }

            $result = Result::where('result_number', $resultNumber)
                ->where('id', $id)
                ->first();

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => '該当データが見つかりません。',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'データの取得に成功しました。',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            Log::error('getDataDetail 例外', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'データ取得中にエラーが発生しました。',
            ], 500);
        }
    }
}
