<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Restaurant as Restaurant;
class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->increments(Restaurant::RESTAURANT_ID);
            $table->string(Restaurant::RESTAURANT_NAME);
            $table->string(Restaurant::RESTAURANT_STREET_ADDRESS);
            $table->longText(Restaurant::RESTAURANT_DESCRIPTION)->nullable();
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
        Schema::drop('restaurants');
    }
}
