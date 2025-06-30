<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReferralCompanyFactory extends Factory
{
    public function definition(): array
    {
        $referralCode = $this->faker->unique()->numerify('##########');

        // ランダムで報酬種別を決定（0 = defaultのみ, 1 = initial, 2 = recurring, 3 = 両方）
        $pattern = $this->faker->numberBetween(0, 3);

        $data = [
            'company_name' => $this->faker->unique()->company,
            'referral_code' => $referralCode,
            'account_create_url' => $this->faker->url . '=%adcode' . $referralCode,
        ];

        if ($pattern === 1 || $pattern === 3) {
            $initialRewardPercentage = $this->faker->randomFloat(0, 10, 30);
            $initialRewardTimes = $this->faker->numberBetween(1, 3);

            $data['initial_reward_percentage'] = $initialRewardPercentage;
            $data['initial_reward_times'] = $initialRewardTimes;
            $data['remaining_reward_times'] = $initialRewardTimes;
        }

        if ($pattern === 2 || $pattern === 3) {
            $data['recurring_reward_percentage'] = $this->faker->randomFloat(0, 1, 5);
        }

        return $data;
    }
}
