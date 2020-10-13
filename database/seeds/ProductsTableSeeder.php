<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

    	for($i = 1; $i <= 2000; $i++){

    		DB::table('product')->insert([
                'product_type' => 1,
                'category_id' => 2,
                'name' => $faker->name,
    			'price' => $faker->numberBetween(1000,20000)
    		]);

    	}

    }
}
