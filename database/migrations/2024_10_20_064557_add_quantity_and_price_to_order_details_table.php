<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityAndPriceToOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->integer('quantity')->after('product_id'); // Adjust the position as needed
            $table->decimal('price', 10, 2)->after('quantity'); // Adjust the position as needed
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('quantity');
            $table->dropColumn('price');
        });
    }
}