<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id(); // idカラム
            $table->string('name'); // 管理者の名前
            $table->string('email')->unique(); // 管理者のメールアドレス（ユニーク）
            $table->timestamp('email_verified_at')->nullable(); // メール認証日時
            $table->string('password'); // パスワード
            $table->rememberToken(); // remember_token
            $table->timestamps(); // created_at, updated_at
            $table->date('birthday')->nullable(); // 誕生日
            $table->string('authority')->default('Viewer'); // 権限
            $table->integer('two_factor_attempts')->default(0); // 2FAの失敗回数
            $table->timestamp('two_factor_lockout_until')->nullable(); // ロックアウトが解除される時間
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('admins');
    }
};
