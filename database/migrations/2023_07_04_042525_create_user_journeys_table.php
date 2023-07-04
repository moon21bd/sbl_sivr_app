<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_journeys', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('user_phone_no')->nullable();
            $table->string('user_account_no')->nullable();
            $table->string('page')->nullable();
            $table->string('action')->nullable();
            $table->text('data')->nullable();
            $table->string('browser')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_journeys');
    }
};
