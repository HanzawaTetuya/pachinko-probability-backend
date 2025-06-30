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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->unsignedBigInteger('user_registration_number'); // ユーザーID (外部キー)
            $table->unsignedBigInteger('product_number'); // 商品番号 (外部キー)
            $table->timestamps(); // created_at, updated_at

            // 外部キー制約を追加
            $table->foreign('user_registration_number')->references('registration_number')->on('users')->onDelete('cascade');
            $table->foreign('product_number')->references('product_number')->on('products')->onDelete('cascade');
            // ユーザーごとに一意の組み合わせを保証
            $table->unique(['user_registration_number', 'product_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
