<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RestaurantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i <= 3 ;$i++){
            DB::table('restaurants')->insert([
                'name' => str_random(10),
                'address' => str_random(10).'vägen '.rand(1,34),
                'description' => str_random(15),
            ]);
        }
    }
}
