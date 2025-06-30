<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ReferralCompany;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ja_JP');

        $referralCodes = ReferralCompany::pluck('referral_code')->toArray();

        if (empty($referralCodes)) {
            throw new \Exception("紹介コードが referral_companies に存在しません。");
        }

        for ($i = 0; $i < 30; $i++) {
            User::create([
                'registration_number' => $faker->unique()->numberBetween(100000, 999999),
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'date_of_birth' => $faker->date(),
                'status' => $faker->randomElement(['active', 'inactive']),
                'password' => Hash::make('password'),
                'referral_code' => $faker->randomElement($referralCodes),
            ]);
        }
    }
}
