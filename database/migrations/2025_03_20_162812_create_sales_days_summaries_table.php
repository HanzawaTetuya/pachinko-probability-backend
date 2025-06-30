<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_days_summaries', function (Blueprint $table) {
            $table->id(); // プライマリキー
            $table->date('date')->unique(); // 売上日
            $table->decimal('total_sales', 10, 2)->default(0); // 日次合計売上
            $table->integer('total_orders')->default(0); // 日次合計注文数
            $table->timestamps(); // `created_at` & `updated_at`
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_days_summaries');
    }
};
