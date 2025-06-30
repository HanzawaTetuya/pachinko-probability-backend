<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Question;

class QuestionController extends Controller
{
    public function getQuestion()
    {
        $categories = Category::with(['questions' => function ($query) {
            $query->select('id', 'category_id', 'question') // answer, order_index を除外
                ->orderBy('id', 'asc');
        }])
            ->select('id', 'name')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }


    public function getAnswer($id)
    {
        $question = Question::select('id', 'question', 'answer', 'created_at')
            ->where('id', $id)
            ->first();

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => '質問が見つかりませんでした。',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $question,
        ]);
    }
}
