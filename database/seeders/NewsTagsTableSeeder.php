<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsTagsTableSeeder extends Seeder
{
    public function run()
    {
        // サンプルデータを挿入
        $newsTags = [
            ['news_id' => 1, 'tag_id' => 1],
            ['news_id' => 1, 'tag_id' => 2],
            ['news_id' => 2, 'tag_id' => 1],
            ['news_id' => 3, 'tag_id' => 3],
        ];

        foreach ($newsTags as $newsTag) {
            DB::table('news_tags')->insert($newsTag);
        }

        // ログ出力
        echo "NewsTagsTableSeeder has been seeded.\n";
    }
}
