<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsercallsTable extends Migration
{
    public function up()
    {
        Schema::create('usercalls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('call_user_id');
            $table->datetime('start_datetime'); // DATETIME type
            $table->datetime('end_datetime');   // DATETIME type
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usercalls');
    }
}
