<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('categories')->insert([
            [
                'name' => '利用方法について',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '支払いについて',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'アカウント・ログイン',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'サービス内容',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
