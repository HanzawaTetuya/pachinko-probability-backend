<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Tag;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function NewsIndex()
    {
        try {
            $newsList = News::with(['tags:id,name'])
                ->select('id', 'title', 'content', 'image_path', 'published_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $newsList,
            ], 200);
        } catch (\Exception $e) {
            Log::error('ニュースデータ取得エラー: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'ニュースデータの取得に失敗しました。',
            ], 500);
        }
    }
}
