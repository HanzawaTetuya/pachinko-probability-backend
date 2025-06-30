<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referral_companies', function (Blueprint $table) {
            $table->id(); // プライマリキー
            $table->string('company_name')->unique(); // 会社名（ユニーク）
            $table->string('referral_code')->unique(); // 企業コード（URLに付与する10桁のコード）
            $table->decimal('initial_reward_percentage', 5, 2)->default(0); // 初回決済の報酬割合（%）
            $table->integer('initial_reward_times')->default(0); // 初回決済の何回分まで適用されるか（1 = 初回のみ, 3 = 初回3回）
            $table->integer('remaining_reward_times')->default(0)->comment('残りの初回決済適用回数'); // 残りの初回決済適用回数
            $table->decimal('recurring_reward_percentage', 5, 2)->default(0); // 継続報酬の割合（%）
            $table->string('account_create_url')->unique()->nullable()->comment('紹介用のアカウント作成URL');
            $table->timestamps(); // `created_at` & `updated_at`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_companies');
    }
};
