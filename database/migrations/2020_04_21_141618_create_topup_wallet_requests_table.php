<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopupWalletRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topup_wallet_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->text('payment_method')->nullable();
            $table->text('trx_id')->nullable();
            $table->text('amount')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('approved_at')->nullable();
            $table->text('status')->nullable();

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
        Schema::dropIfExists('topup_wallet_requests');
    }
}
