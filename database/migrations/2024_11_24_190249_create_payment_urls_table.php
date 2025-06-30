<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payment_urls', function (Blueprint $table) {
            $table->id(); // 自動インクリメントID
            $table->string('name')->unique(); // 商品構成や識別用の一意な名前
            $table->string('url'); // Stripeで生成されたリンクのURL
            $table->timestamps(); // 作成・更新日時
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_urls');
    }
};
