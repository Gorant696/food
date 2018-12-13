<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

/*
	Repository responsible for filtering and building Foods object
*/
class FoodsRepository 
{
	/*
        Function checks if there are tags array inside request and returns array of food IDs based on search query. If array is empty or is not set, function will take all ID's of foods and return them as array value.
    */
    public function filterFoodsByTags($request, $tags, $foods)
    {
        //If tags parameter is true and contain IDs of tags, filter foods based on tag IDs
        if (isset($request->tags) && !empty($request->tags)) {

            //Defined arrays
            $food_id_array = [];
            $final_food_ids = [];

            //Find all foods entites attached to tag ID's from request
            if ($request->diff_time) {
                $diff_time = $request->diff_time;

                $collection_tags = $tags->whereIn('id', $request->tags)
                ->with(['foods' => function ($query) use ($diff_time) {
                    $query->withTrashed()
                        ->whereDate('foods.created_at', '>=', Carbon::parse($diff_time))
                        ->orWhereDate('foods.updated_at', '>=', Carbon::parse($diff_time))
                        ->orWhereDate('foods.deleted_at', '>=', Carbon::parse($diff_time));
                }])->get()->pluck('foods')->toArray();
            } else {
                $collection_tags = $tags->whereIn('id', $request->tags)->with('foods')->get()->pluck('foods')->toArray();
            }

            //Loop through foods entites, extract their ID's and organize them in one layer array
            foreach ($collection_tags as $value) {
                foreach ($value as $food_array) {
                    array_push($food_id_array, $food_array['id']);
                }
            }

            //Count and organize ID's based on duplicate values in food_id_array, and check condition if there are count of same values that corresponds to count of elements of tags array from request. Result is organized final_food_ids array containing ID's of foods resource which have all tags from request attached to themselves in pivot table
            foreach (array_count_values($food_id_array) as $food_id => $count) {
                if (count($request->tags) === (int)$count) {
                    array_push($final_food_ids, $food_id);
                }
            }

            return $final_food_ids;
        } // End of if statement

        else {
            //Get the IDs of foods
            if ($request->diff_time) {
                return $foods->withTrashed()
                ->whereDate('foods.created_at', '>=', Carbon::parse($request->diff_time))
                ->orWhereDate('foods.updated_at', '>=', Carbon::parse($request->diff_time))
                ->orWhereDate('foods.deleted_at', '>=', Carbon::parse($request->diff_time))
                ->get()->pluck('id')->toArray();
            } else {

                return $foods->get()->pluck('id')->toArray();
            }
        }
    }

    /*
        Function responsible for modifying query to filter food based on categories
    */
    public function categoriesCheck($request, $final_foods)
    {
        if ($request->category_id) {
            $final_foods = $final_foods->where('category_id', $request->category_id);
        }

        return $final_foods;
    }

    /*
        Function responsible for paginating collection of food objects when valid parameters are sent via request
    */
    public function paginator($request, $final_foods)
    {
        if ($request->page) {
            $final_foods = $final_foods->paginate($request->per_page);
        } else {
            $final_foods = $final_foods->get();
        }

        return $final_foods;
    }

    /*
        Function responsible for formating and creating valid response JSON object based on request parameters
    */
    public function formatResponse($request, $final_foods, $tags_trans, $ingredients_trans)
    {
        //Set category sub-object of food object
        $this->categories($request, $final_foods); 
        //Set ingredients sub-object of food object
        $this->ingredients($request, $final_foods);
        //Set tags sub-object of food object
        $this->tags($request, $final_foods);

        if ($request->diff_time) {
            $final_foods->loadMissing(['foods' => function ($query) use ($request) {
                $query->withTrashed();
            }]);
        } else {
            $final_foods->loadMissing('foods');
        }
        
        foreach ($final_foods as $food_object) {
            //Loop through collection of foods and set status property of food object
            $this->status($request, $food_object);
        }

        return response()->json(['data' => $final_foods]);
    }

    /*
        Function responsible for creating categories sub-object for food object
    */
    private function categories($request, $final_foods)
    {
        if (isset($request->with) && in_array('categories', $request->with)) {
            $final_foods->load(['categories.categoriesTrans' => function ($query) use ($request) {
                $query->where('language_id', $request->language_id)->first();
            }]);
        }

        return true;
    }

    /*
        Function responsible for creating tags sub-object for food object
    */
    private function tags($request, $final_foods)
    {
        if (isset($request->with) && in_array('tags', $request->with)) {
            $this->checkDiffTime($request, $final_foods)
            ->loadMissing(['foods.tags.tagsTrans' => function ($query) use ($request) {
                $query->where('language_id', $request->language_id)->get();
            }]);
        }

        return true;
    }

    /*
        Function responsible for modifying object model if diff time param is sent via request
    */
    private function checkDiffTime($request, $final_foods)
    {
        if ($request->diff_time) {
            $final_foods = $final_foods->loadMissing(['foods' => function($query) use ($request) {
                $query->withTrashed();
            }]);
        }

        return $final_foods;
    }

    /*
        Function responsible for creating ingredients sub-object for food object
    */
    private function ingredients($request, $final_foods)
    {
        if (isset($request->with) && in_array('ingredients', $request->with)) {
               $this->checkDiffTime($request, $final_foods)
               ->loadMissing(['foods.ingredients.ingredientsTrans' => function ($query) use ($request) {
                $query->where('language_id', $request->language_id)->get();
            }]);
        }

        return true;
    }

    /*
        Function responsible for creating status property for food object
    */
    private function status($request, $food_object)
    {
        if ($request->diff_time) {
            $food = $food_object->foods;

            if ($food->deleted_at) {
                $food_object->status = 'deleted';
                return true;
            }

            if ($food->created_at->format('Y-m-d H:i:s') == $food->updated_at->format('Y-m-d H:i:s')) {
                $food_object->status = 'created';
                return true;
            }

            if ($food->created_at->format('Y-m-d H:i:s') < $food->updated_at->format('Y-m-d H:i:s')) {
                $food_object->status = 'updated';
                return true;
            }
        } else {
            //Default status if diff_time parameter is not send via request
            $food_object->status = 'created';
        }
    }

    /*
        Inserting new food models into foods table
    */
    public function store($request, $foods, $foods_trans)
    {
    	//Create food entity in foods table if does not exists
        if (!$foods->where('slug', $request->slug)->count()) {
            $food_model = $foods->create([
                'slug' => $request->slug
            ]);

            if ($food_model) {

                //Create translations for food model
                $food_model->foodsTrans()->create($request->food_trans_hr);
                $food_model->foodsTrans()->create($request->food_trans_en);

                //Attach tag and ingredient ID's into pivot table
                foreach ($request->tags as $tag) {
                    $food_model->tags()->attach($tag);
                }

                foreach ($request->ingredients as $ingredient) {
                    $food_model->ingredients()->attach($ingredient);
                }

                //Setting properties for returning model
                $this->status($request, $food_model);
                $food_model->categories = $food_model->foodsTrans()->first()->categories()->first();
                $food_model->load(['ingredients', 'tags']);

                return $food_model;
            }
        }
    }
}
