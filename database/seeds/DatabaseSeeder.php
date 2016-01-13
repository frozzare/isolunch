<?php

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        Model::unguard();

        $this->call(RestaurantsTableSeeder::class);
        $this->call(CategoriessTableSeeder::class);

//         $this->call(UserTableSeeder::class);
//        Model::reguard();

    }
}
