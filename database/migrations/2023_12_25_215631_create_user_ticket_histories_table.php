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
        Schema::create('user_ticket_histories', function (Blueprint $table) {
            $table->id();
            $table->string('mobile_no', 20)->nullable();
            $table->string('purpose', 128);
            $table->string('status')->default(0);
            $table->timestamps();

            $table->index(['mobile_no']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_ticket_histories');
    }
};
