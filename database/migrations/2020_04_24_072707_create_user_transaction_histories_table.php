<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('topup_request_id')->nullable();
            $table->unsignedBigInteger('withdrawal_request_id')->nullable();
            $table->text('transaction_type')->nullable();
            $table->text('trx_id')->nullable();
            $table->text('amount')->nullable();
            $table->text('remaining_balance')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('user_transaction_histories');
    }
}
