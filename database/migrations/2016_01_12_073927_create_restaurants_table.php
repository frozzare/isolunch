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
            $table->string(Restaurant::RESTAURANT_NAME)->nullable();
            $table->string(Restaurant::RESTAURANT_STREET_ADDRESS)->nullable();
            $table->integer(Restaurant::RESTAURANT_LAT)->nullable();
            $table->integer(Restaurant::RESTAURANT_LONG)->nullable();
            $table->string(Restaurant::RESTAURANT_WEB_SITE)->nullable();
            $table->string(Restaurant::RESTAURANT_GOOGLE_PLACE_ID)->nullable();
            $table->string(Restaurant::RESTAURANT_IMAGE)->nullable();
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
