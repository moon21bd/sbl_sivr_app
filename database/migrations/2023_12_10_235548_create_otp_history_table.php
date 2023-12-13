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
        Schema::create('otp_history', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 15)->unique();
            $table->integer('otp_sent_count')->default(0);
            $table->timestamp('last_sent_at')->nullable();
            $table->boolean('otp_sent_success')->default(false);
            $table->integer('response_status_code')->nullable();
            $table->timestamp('response_received_at')->nullable();
            $table->text('response_data')->nullable();
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
        Schema::dropIfExists('otp_history');
    }
};
