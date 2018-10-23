<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuckTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->timestamp('completed_at')->nullable();
            $table->string('gateway_transaction_id');
            $table->unsignedInteger('customer_id')->nullable();
            $table->uuid('discount_id')->nullable();
            $table->decimal('discount', 7, 2)->nullable();

            $table->foreign('customer_id')->references('id')->on('users');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('order_id');
            $table->uuid('product_id');
            $table->integer('quantity');
            $table->integer('price');
            $table->integer('total');

            $table->foreign('order_id')->references('id')->on('orders');
        });

        Schema::create('download_links', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_item_id');
            $table->dateTime('expires_at');

            $table->foreign('order_item_id')->references('id')->on('order_items');
        });

        // add the customer specific fields to the user table
        Schema::table('users', function (Blueprint $table) {
            $table->string('gateway_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('download_links');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
}
