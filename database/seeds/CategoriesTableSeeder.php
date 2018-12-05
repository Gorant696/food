<?php

use Illuminate\Database\Seeder;
use App\Categories;
use App\Meta\Metadata;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Categories $categories, Metadata $meta)
    {
    	foreach ($meta->listOfCategories() as $key => $one_category) {
    		$category_main = $categories->create(['slug' => $key]);
    		foreach ($one_category as $one) {
    			$category_main->categoriesTrans()->create([
    				'language_id' => $one['language_id'],
    				'title' => $one['title']
    			]);
    		}
    	}
    }
}
