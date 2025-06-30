<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referral_company_monthly_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('referral_code');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->integer('total_orders')->default(0);
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->decimal('total_rewards', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['referral_code', 'year', 'month'], 'referral_monthly_summary_unique');
            $table->foreign('referral_code')->references('referral_code')->on('referral_companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_company_monthly_summaries');
    }
};
