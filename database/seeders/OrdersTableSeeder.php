<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ja_JP');

        $userRegNumbers = DB::table('users')->pluck('registration_number')->toArray();
        $referralCodes = DB::table('referral_companies')->pluck('referral_code')->toArray();

        if (empty($userRegNumbers) || empty($referralCodes)) {
            throw new \Exception("ユーザーまたは紹介コードが存在しません。");
        }

        foreach (range(1, 20) as $i) {
            DB::table('orders')->insert([
                'user_id' => $faker->randomElement($userRegNumbers),
                'order_number' => $faker->unique()->numberBetween(100000, 999999),
                'total_price' => 33000 * rand(1, 10), // 33000円単位
                'status' => $faker->randomElement(['pending', 'completed', 'cancelled']),
                'referral_code' => $faker->randomElement($referralCodes),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
