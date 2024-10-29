<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('name')->nullable(); // Name of the menu
            $table->string('description')->nullable(); // Description of the menu
            $table->text('image')->nullable(); // Imaeg of the menu
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('menus');
    }
}