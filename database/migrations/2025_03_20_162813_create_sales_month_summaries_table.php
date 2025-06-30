<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_month_summaries', function (Blueprint $table) {
            $table->id(); // プライマリキー
            $table->year('year'); // 売上年
            $table->integer('month'); // 売上月
            $table->decimal('total_sales', 10, 2)->default(0); // 月次合計売上
            $table->integer('total_orders')->default(0); // 月次合計注文数
            $table->timestamps(); // `created_at` & `updated_at`
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_month_summaries');
    }
};
