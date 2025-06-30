<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReferralCompany;

class ReferralCompanySeeder extends Seeder
{
    public function run(): void
    {
        ReferralCompany::factory()->count(5)->create();
    }
}
