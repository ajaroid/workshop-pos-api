<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsPurchasesAndSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('supplier_id');
            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('cascade');
            $table->bigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->bigInteger('total')->default(0);
            $table->timestamps();
        });

        Schema::create('trx_purchase_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('trx_purchase_id');
            $table->foreign('trx_purchase_id')
                ->references('id')
                ->on('trx_purchases')
                ->onDelete('cascade');
            $table->bigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
            $table->integer('qty')->unsigned()->default(0);
            $table->bigInteger('subtotal')->default(0);
            $table->timestamps();
        });

        Schema::create('trx_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->bigInteger('total')->default(0);
            $table->bigInteger('pay')->default(0);
            $table->bigInteger('change')->default(0);
            $table->timestamps();
        });

        Schema::create('trx_sale_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('trx_sale_id');
            $table->foreign('trx_sale_id')
                ->references('id')
                ->on('trx_sales')
                ->onDelete('cascade');
            $table->bigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
            $table->integer('qty')->unsigned()->default(0);
            $table->bigInteger('subtotal')->default(0);
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
        Schema::table('trx_purchases', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::table('trx_purchase_details', function (Blueprint $table) {
            $table->dropForeign(['trx_purchase_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::table('trx_sales', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('trx_sale_details', function (Blueprint $table) {
            $table->dropForeign(['trx_sale_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::dropIfExists('trx_purchases');
        Schema::dropIfExists('trx_sales');
    }
}
