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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->string('license_id', 255)->unique();
            $table->timestamps();

            $table->foreign('user_id')->references('registration_number')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('order_number')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('product_number')->on('products')->onDelete('cascade');
            $table->foreign('license_id')->references('license_key')->on('licenses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
