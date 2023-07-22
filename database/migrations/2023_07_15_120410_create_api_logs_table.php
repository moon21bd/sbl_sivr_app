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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip')->nullable();
            $table->string('url')->nullable();
            $table->integer('status_code')->default(0);
            $table->float('response_time')->nullable();
            $table->text('request')->nullable();
            $table->text('response')->nullable();
            $table->string('exception_type')->nullable();
            $table->longText('server_info')->nullable();
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
        Schema::dropIfExists('api_logs');
    }
};
