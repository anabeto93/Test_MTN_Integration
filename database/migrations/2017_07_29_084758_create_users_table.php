

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company', 191);
            $table->string('nick_name', 30);
            $table->string('apiuser', 191);
            $table->string('merchant_id', 12);
            $table->string('wallet_id', 12);
            $table->string('acc_number', 30);
            $table->string('pass_code', 35);
            $table->string('mcc', 12);
            $table->string('email', 191);
            $table->string('password', 191)->default('y$kNl7EqlgSOxLizno7/rxOOV/gKtvW3bA3bOokCW.3jSANkJGPvZca');
            $table->string('contact', 191)->default('Telephone');
            $table->string('address', 191)->default('Address');
            $table->string('role', 191)->default('user');
            $table->integer('state')->default('0');
            $table->string('remember_token', 100);
            $table->timestamps();
            $table->unique(["company"]);
            $table->unique(["apiuser"]);
            $table->unique(["merchant_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

