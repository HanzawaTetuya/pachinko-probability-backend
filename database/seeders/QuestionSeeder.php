<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            1 => [
                ['question' => 'サービスの利用方法を教えてください。', 'answer' => 'サービスは会員登録後にご利用いただけます。'],
                ['question' => '利用料金はいくらですか？', 'answer' => '利用料金は月額980円です。']
            ],
            2 => [
                ['question' => '支払い方法を教えてください。', 'answer' => 'クレジットカード、銀行振込がご利用可能です。'],
                ['question' => '請求書の発行は可能ですか？', 'answer' => 'マイページからPDFでダウンロードできます。']
            ],
            3 => [
                ['question' => 'ログインできない場合どうすればいいですか？', 'answer' => 'パスワード再設定をご利用ください。'],
                ['question' => '退会方法を教えてください。', 'answer' => 'マイページから退会手続きが可能です。']
            ],
            4 => [
                ['question' => 'サービス内容の概要を教えてください。', 'answer' => '本サービスはオンラインで学習や業務支援を提供する総合プラットフォームです。'],
                ['question' => 'どのようなユーザーが対象ですか？', 'answer' => '法人・個人問わず、幅広い利用者にご利用いただけます。']
            ]
        ];

        foreach ($questions as $categoryId => $qs) {
            foreach ($qs as $index => $q) {
                DB::table('questions')->insert([
                    'category_id' => $categoryId,
                    'question' => $q['question'],
                    'answer' => $q['answer'],
                    'order_index' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
