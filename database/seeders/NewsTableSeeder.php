<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsTableSeeder extends Seeder
{
    public function run()
    {
        // サンプルデータ
        $newsItems = [
            [
                'title' => '最新ニュース1',
                'content' => 'これは最新ニュース1の内容です。',
                'image_path' => 'images/news1.jpg',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '最新ニュース2',
                'content' => 'これは最新ニュース2の内容です。',
                'image_path' => 'images/news2.jpg',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '最新ニュース3',
                'content' => 'これは最新ニュース3の内容です。',
                'image_path' => 'images/news3.jpg',
                'published_at' => null, // まだ公開されていないニュース
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // データ挿入
        DB::table('news')->insert($newsItems);

        // ログ出力
        echo "NewsTableSeeder has been seeded.\n";
    }
}
