<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ttm_wallets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('merchant_id', 12);
            $table->string('user_id', 255);
            $table->string('wallet_id', 32);
            $table->string('pass_code', 64);
            $table->string('details', 255);
            $table->timestamps();
            $table->unique(["wallet_id"]);
            $table->index(["merchant_id"]);
            $table->index(["user_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}
