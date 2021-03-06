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
        Schema::create('discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('limit_type');
            $table->string('limit')->nullable();
            $table->decimal('amount');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('status');
            $table->timestamp('completed_at')->nullable();
            $table->string('gateway_transaction_id');
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('discount_id')->nullable();
            $table->decimal('discount_amount')->nullable();

            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('discount_id')->references('id')->on('discounts');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('order_id');
            $table->uuid('product_id');
            $table->integer('quantity');
            $table->decimal('price');
            $table->decimal('total');

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
        Schema::dropIfExists('statuses');
        Schema::dropIfExists('discount_limit_types');
        Schema::dropIfExists('discount_types');
    }
}
