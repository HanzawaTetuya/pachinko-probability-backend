<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            // AdminSeeder::class,
            // NewsTableSeeder::class,
            // NewsTagsTableSeeder::class,
            // ReferralCompanySeeder::class,
            // UserSeeder::class,
            // OrdersTableSeeder::class,
            // CategorySeeder::class,
            QuestionSeeder::class,
    ]);  // UserSeederを呼び出す
    }
}