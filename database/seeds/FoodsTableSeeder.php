<?php

use Illuminate\Database\Seeder;
use App\Foods;
use App\Categories;
use App\Tags;
use App\Meta\Metadata;

class FoodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Foods $foods, Categories $categories, Metadata $meta, Tags $tags)
    {
   
        foreach ($meta->list_of_food($categories) as $key => $one_meal) {
            $food_main = $foods->create(['slug' => $key]);

            $tag_array = $tags->get()->pluck('id')->toArray();
            $random_tag_key = rand(0, count($tag_array)-1);

            $food_main->tags()->attach($tag_array[$random_tag_key]);


            foreach ($one_meal as $one) {
                $food_main->foods_trans()->create([
                    'language_id' => $one['language_id'],
                    'title' => $one['title'],
                    'description' => $one['description'],
                    'category_id' => $one['category_id']
                ]);
            }
        }
    }
}
