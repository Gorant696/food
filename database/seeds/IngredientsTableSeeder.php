<?php

use Illuminate\Database\Seeder;
use App\Ingredients;
use App\Foods;
use App\Meta\Metadata;

class IngredientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Ingredients $ingredients, Foods $foods, Metadata $meta)
    {
    	foreach ($meta->listOfIngredients() as $key => $one_ingredient) {
    		$ingredient_main = $ingredients->create(['slug' => $key]);

    		foreach ($this->attachToPivot($foods, Metadata::ingredientsForFood()[$key]) as $value) {
    			$ingredient_main->foods()->attach($value);
    		}

    		foreach ($one_ingredient as $one) {
    			$ingredient_main->ingredientsTrans()->create([
    				'language_id' => $one['language_id'],
    				'title' => $one['title']
    			]);
    		}
    	}
    }

    private function attachToPivot($foods, $array)
    {
    	return $foods->whereIn('slug', $array)->get()->pluck('id')->toArray();
    }

}
