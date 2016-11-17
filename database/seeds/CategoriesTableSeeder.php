<?php

use Illuminate\Database\Seeder;
use App\Category as Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->delete();

        $faker = Faker\Factory::create();

        $categories = array(
        	['name' => 'Lunch'],
        	['name' => 'Fuel'],
        	['name' => 'Church Collection'],
        	['name' => 'Donation'],
        	['name' => 'Internet'],
        	['name' => 'Parking'],
        	['name' => 'Prepaid Credit'],
        	['name' => 'Book'],
        	['name' => 'Snack'],
        	['name' => 'Clothes'],
        	['name' => 'Etc'],
        	['name' => 'Having Fun'],
        	['name' => 'Investation'],
        	['name' => 'Health'],
        );

		foreach ($categories as $category) {
			Category::create($category);
		}
    }
}
