<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp_history', function (Blueprint $table) {
            $table->id();
            $table->string('mobile_no', 20);
            $table->string('otp', 20)->nullable();
            $table->string('purpose', 64)->nullable();
            $table->enum('otp_used', ['yes', 'no']);
            $table->integer('api_status_code')->nullable();
            $table->text('api_response')->nullable();
            $table->timestamp('otp_sent_at')->nullable(); // Corrected line

            $table->index('mobile_no');
        });
    }

    public function down()
    {
        Schema::dropIfExists('otp_history');
    }
};
