<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // 自動的に AUTO_INCREMENT PRIMARY KEY を設定
            $table->unsignedBigInteger('product_number')->unique();
            $table->string('name');
            $table->string('manufacturer');
            $table->string('category');
            $table->decimal('price', 8, 2);
            $table->date('release_date');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('python_file_path');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
