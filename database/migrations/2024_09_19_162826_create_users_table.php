<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registration_number')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->date('date_of_birth');
            $table->string('password');
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
            $table->string('referral_code')->nullable();
            $table->timestamps();

            $table->foreign('referral_code')
                ->references('referral_code')
                ->on('referral_companies')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
