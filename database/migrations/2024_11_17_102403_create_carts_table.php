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
        Schema::create('carts', function (Blueprint $table) {
            $table->id(); // プライマリキー
            $table->unsignedBigInteger('user_registration_number'); // ユーザーの登録番号
            $table->unsignedBigInteger('product_number'); // 商品番号
            $table->integer('quantity')->default(1); // 数量（デフォルトは1）
            $table->timestamps();

            // 外部キー制約
            $table->foreign('user_registration_number')->references('registration_number')->on('users')->onDelete('cascade');
            $table->foreign('product_number')->references('product_number')->on('products')->onDelete('cascade');

            // ユーザーと商品の組み合わせが一意
            $table->unique(['user_registration_number', 'product_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
