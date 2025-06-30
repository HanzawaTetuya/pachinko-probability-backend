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
        Schema::create('questions', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->unsignedBigInteger('category_id'); // 外部キー
            $table->string('question'); // 質問
            $table->text('answer')->nullable(); // 回答
            $table->integer('order_index')->default(0); // 表示順
            $table->timestamps(); // created_at と updated_at

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
