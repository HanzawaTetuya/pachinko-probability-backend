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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->string('result_number', 18);// 12桁の一意な番号
            $table->string('machine_number');             // 機械番号
            $table->string('product_name');               // 商品名
            $table->float('hit_probability');             // 100回以内に大当たりを引く確率
            $table->float('expected_chain_count');      // 次の当たりの連チャン数
            $table->float('chain_probability');           // その連チャン数が発生する確率
            $table->string('current_bonus',255);             // 現在の台のお得玉数
            $table->string('cash_balance_3_3',255);   // 換金率3.3円の場合の収支
            $table->timestamps();                         // created_at と updated_at

            // result_number に外部キー制約を追加
            $table->foreign('result_number')->references('result_number')->on('results_usage')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
