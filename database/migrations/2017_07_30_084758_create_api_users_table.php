

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_name', 100);
            $table->string('api_key', 100);
            $table->timestamp('created_on')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('status')->default('1');
            $table->string('actions', 30);
            $table->double('amount_limit')->default('500');
            $table->timestamps();
            $table->unique(["user_name"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_users');
    }
}

