

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsmtnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_mtn', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 255);
            $table->string('password', 255);
            $table->string('name', 255);
            $table->string('info', 255);
            $table->string('mobile', 255);
            $table->string('amt', 10);
            $table->string('thirdpartyID', 255);
            $table->string('billprompt', 255);
            $table->string('mesg', 255);
            $table->string('expiry', 255);
            $table->string('invoiceNo', 255);
            $table->string('responseCode', 255);
            $table->string('responseMessage', 255);
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
        Schema::dropIfExists('logs_mtn');
    }
}

