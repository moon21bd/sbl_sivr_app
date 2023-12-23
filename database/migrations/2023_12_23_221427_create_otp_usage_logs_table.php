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
        Schema::create('otp_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->string('mobile_no', 20)->nullable();
            $table->string('otp', 20)->nullable();
            $table->string('purpose', 64)->nullable();
            $table->enum('is_valid', ['yes', 'no']);
            $table->integer('api_status_code')->nullable();
            $table->text('api_response')->nullable();
            $table->timestamp('otp_updated_at')->useCurrent()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('otp_usage_logs');
    }
};
