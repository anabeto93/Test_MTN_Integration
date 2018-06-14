<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTtmTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ttm_transactions', function (Blueprint $table) {
            $table->string('fld_002')->comment('Wallet Number');
            $table->string('fld_003')->comment('Transaction Type');
            $table->string('fld_004')->comment('Transaction Amount');
            $table->string('fld_009')->comment('Device Type');
            $table->string('fld_011')->primary()->comment('System Trace Audit Number');
            $table->string('fld_012')->comment('Transaction Date');
            $table->string('fld_014', 5)->nullable()->comment('Card Expiration Date');
            $table->string('fld_037')->comment('Reference');
            $table->string('fld_038')->nullable()->comment('Approved Code');
            $table->string('fld_039')->nullable()->default('101')->comment('Response Code');
            $table->string('fld_041', 8)->nullable()->default('null')->comment('Terminal ID');
            $table->string('fld_042', 12)->nullable()->default('null')->comment('Merchant ID');
            $table->string('fld_043')->nullable()->comment('Merchant Name and Location');
            $table->string('fld_057')->comment('R - Switch');
            $table->string('fld_103', 100)->nullable()->comment('To-Account Number');
            $table->string('fld_116')->comment('Description');
            $table->string('fld_117', 50)->nullable();
            $table->string('fld_123', 4)->nullable();
            $table->text('rfu_001', 65535)->nullable()->comment('Reserved For Future Use');
            $table->text('rfu_002', 65535)->nullable()->comment('Reserved For Future Use');
            $table->text('rfu_003', 65535)->nullable()->comment('Reserved For Future Use');
            $table->text('rfu_004', 65535)->nullable()->comment('Reserved For Future Use');
            $table->text('rfu_005', 65535)->nullable()->comment('Reserved For Future Use');
            $table->index(['fld_012','fld_037','fld_043'], 'ttm_transactions_fld_012_fld_037_fld_018_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ttm_transactions');
    }
}
