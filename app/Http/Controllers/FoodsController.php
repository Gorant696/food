<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Foods;
use App\Tags;
use App\FoodsTrans;
use App\TagsTrans;
use App\IngredientsTrans;
use Carbon\Carbon;

class FoodsController extends Controller
{
    public const ID = 'id';
    public const FOOD_ID = 'food_id';
    public const LANGUAGE_ID = 'language_id';
    public const TAG_ID = 'tag_id';
    public const CATEGORY_ID = 'category_id';
    public const INGREDIENT_ID = 'ingredient_id';
    public const SLUG = 'slug';
    public const TITLE = 'title';
    public const DESC = 'description';
    public const FOODS = 'foods';
    public const TAGS = 'tags';
    public const CATEGORIES = 'categories';
    public const INGREDIENTS = 'ingredients';
    public const DELETED = 'deleted';
    public const CREATED = 'created';
    public const UPDATED = 'updated';

    public function index(
            Request $request,
            Foods $foods,
            Tags $tags,
            FoodsTrans $foods_trans,
            TagsTrans $tags_trans,
            IngredientsTrans $ingredients_trans
        ) {
       
        //Filter foods by tags
        $final_food_ids = $this->filterFoodsByTags($request, $tags, $foods);

        //Model of query for collection of foods resource
        $final_foods = $foods_trans->whereIn(self::FOOD_ID, $final_food_ids)->where(self::LANGUAGE_ID, $request->language_id);

        //Modify query based on category_id from request
        $final_foods = $this->categoriesCheck($request, $final_foods);

        //Paginate collection based on parameters from request
        $final_foods = $this->paginator($request, $final_foods);

        return $this->formatResponse($request, $final_foods, $tags_trans, $ingredients_trans);
    }


    /*
        Function checks if there are tags array inside request and returns array of food IDs based on search query. If array is empty or is not set, function will take all ID's of foods and return them as array value.
    */
    private function filterFoodsByTags($request, $tags, $foods)
    {
        //If tags parameter is true and contain IDs of tags, filter foods based on tag IDs
        if (isset($request->tags) && !empty($request->tags)) {

            //Defined arrays
            $food_id_array = [];
            $final_food_ids = [];

            //Find all foods entites attached to tag ID's from request
            if ($request->diff_time) {
                $diff_time = $request->diff_time;

                $collection_tags = $tags->whereIn(self::ID, $request->tags)->with([self::FOODS => function ($query) use ($diff_time) {
                    $query->withTrashed()
                        ->whereDate('foods.created_at', '>=', Carbon::parse($diff_time))
                        ->orWhereDate('foods.updated_at', '>=', Carbon::parse($diff_time))
                        ->orWhereDate('foods.deleted_at', '>=', Carbon::parse($diff_time));
                }])->get()->pluck(self::FOODS)->toArray();
            } else {
                $collection_tags = $tags->whereIn(self::ID, $request->tags)->with(self::FOODS)->get()->pluck(self::FOODS)->toArray();
            }

            //Loop through foods entites, extract their ID's and organize them in one layer array
            foreach ($collection_tags as $value) {
                foreach ($value as $food_array) {
                    array_push($food_id_array, $food_array[self::ID]);
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

                return $foods->get()->pluck(self::ID)->toArray();
            }
        }
    }

    /*
        Function responsible for modifying query to filter food based on categories
    */
    private function categoriesCheck($request, $final_foods)
    {
        if ($request->category_id) {
            $final_foods = $final_foods->where(self::CATEGORY_ID, $request->category_id);
        }

        return $final_foods;
    }

    /*
        Function responsible for paginating collection of food objects when valid parameters are sent via request
    */
    private function paginator($request, $final_foods)
    {
        if ($request->page) {
            $final_foods = $final_foods->paginate($request->per_page);
        } else {
            $final_foods = $final_foods->get();
        }

        return $final_foods;
    }

    /*
        Function responsible for creating categories sub-object for food object
    */
    private function categories($request, $food_object)
    {
        if (isset($request->with) && in_array(__FUNCTION__, $request->with)) {
            if ($food_object->category_id) {
                $food_object->category = $food_object->categories()->first()->categoriesTrans()->where(self::LANGUAGE_ID, $request->language_id)->with(self::CATEGORIES)->first();
            }
        }

        return true;
    }

    /*
        Function responsible for creating tags sub-object for food object
    */
    private function tags($request, $food_object, $tags_trans)
    {
        if (isset($request->with) && in_array(__FUNCTION__, $request->with)) {
            $food_object->tags = $tags_trans
            ->whereIn(self::TAG_ID, $this->checkDiffTime($request, $food_object)->first()->tags()->get()->pluck(self::ID)->toArray())
            ->where(self::LANGUAGE_ID, $request->language_id)->with(self::TAGS)->get();
        }

        return true;
    }

    /*
        Function responsible for creating ingredients sub-object for food object
    */
    private function ingredients($request, $food_object, $ingredients_trans)
    {
        if (isset($request->with) && in_array(__FUNCTION__, $request->with)) {
            $food_object->with = $ingredients_trans
            ->whereIn(self::INGREDIENT_ID, $this->checkDiffTime($request, $food_object)->first()->ingredients()->get()->pluck(self::ID)->toArray())
            ->where(self::LANGUAGE_ID, $request->language_id)->with(self::INGREDIENTS)->get();
        }

        return true;
    }

    /*
        Modified object for foods based on diff_time
    */
    private function checkDiffTime($request, $food_object)
    {
        $main_object = $food_object->foods();

        if ($request->diff_time) {
            $main_object = $main_object->withTrashed();
        }

        return $main_object;
    }

    /*
        Function responsible for creating status property for food object
    */
    private function status($request, $food_object)
    {
        if ($request->diff_time) {
            $food = $food_object->foods()->withTrashed()->first();

            if ($food->deleted_at) {
                $food_object->status = self::DELETED;
                return true;
            }

            if ($food->created_at->format('Y-m-d H:i:s') == $food->updated_at->format('Y-m-d H:i:s')) {
                $food_object->status = self::CREATED;
                return true;
            }

            if ($food->created_at->format('Y-m-d H:i:s') < $food->updated_at->format('Y-m-d H:i:s')) {
                $food_object->status = self::UPDATED;
                return true;
            }
        } else {
            //Default status if diff_time parameter is not send via request
            $food_object->status = self::CREATED;
        }
    }

    /*
        Function responsible for formating and creating valid response JSON object based on request parameters
    */
    private function formatResponse($request, $final_foods, $tags_trans, $ingredients_trans)
    {
        foreach ($final_foods as $food_object) {
            //Set status property of food object
            $this->status($request, $food_object);
            //Set category sub-object of food object
            $this->categories($request, $food_object);
            //Set tags sub-object of food object
            $this->tags($request, $food_object, $tags_trans);
            //Set ingredients sub-object of food object
            $this->ingredients($request, $food_object, $ingredients_trans);
        }
        
        return response()->json(['data' => $final_foods]);
    }

    /*
        Inserting new food models into foods table
    */
    public function store(Request $request, Foods $foods, FoodsTrans $foods_trans)
    {
        //Create food entity in foods table if does not exists
        if (!$foods->where(self::SLUG, $request->slug)->count()) {
            $food_model = $foods->create([
                self::SLUG => $request->slug
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
                $food_model->translations = $food_model->foodsTrans()->with(self::CATEGORIES)->get();
                $food_model->ingredients = $food_model->ingredients()->get();
                $food_model->tags = $food_model->tags()->get();
                    
                return response()->json(['data' => $food_model]);
            }
        }
     
        return response()->json(['message' => 'Food already exists!'], 400);
    }

    /*
        Soft deleting food models from foods table
    */
    public function delete(Request $request, Foods $foods)
    {
        //Models are soft deleted
        foreach ($request->ids as $id) {
            $foods->findOrFail($id)->delete();
        }

        return response()->json(['message' => 'Deleted successfully!'], 200);
    }

    /*
        Restoring soft-deleted food models from foods table
    */
    public function restore(Request $request, Foods $foods)
    {
        //Restore deleted models
        $foods->withTrashed()->whereIn('id', $request->ids)->restore();

        return response()->json(['message' => 'Restored successfully!'], 200);
    }
}
