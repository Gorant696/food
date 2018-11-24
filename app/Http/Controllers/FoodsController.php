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
    public function index(Request $request, Foods $foods, Tags $tags, FoodsTrans $foods_trans, TagsTrans $tags_trans, IngredientsTrans $ingredients_trans) {


        /*

        definirati konstante za food_id, language_id i ostale kolumne
        
        validacije

        updejtat read me sa samplovima objekata iz rekvesta i responsa

        stavit na git, sredit i tamo opise


        */

        $final_food_ids = $this->filter_foods_by_tags($request, $tags, $foods);

        //Model of query for collection of foods resource
        $final_foods = $foods_trans->whereIn('food_id', $final_food_ids)->where('language_id', $request->language_id);

        //Modify query based on category_id from request
        $final_foods = $this->categories_check($request, $final_foods);

        //Paginate collection based on parameters from request
        $final_foods = $this->paginator($request, $final_foods);

        return $this->format_response($request, $final_foods, $tags_trans, $ingredients_trans);
    }

    private function filter_foods_by_tags($request, $tags, $foods)
    {

        //If tags parameter is true and contain IDs of tags, filter foods based on tag IDs
        if (isset($request->tags) && !empty($request->tags)) {

                //Defined arrays
                $food_id_array = [];
                $final_food_ids = [];

                //Find all foods entites attached to tag ID's from request
                if ($request->diff_time) {

                    $diff_time = $request->diff_time;

                    $collection_tags = $tags->whereIn('id', $request->tags)->with(['foods' => function ($query) {
                        global $diff_time;
                        $query->withTrashed()->whereDate('foods.created_at', '>=', Carbon::parse($diff_time))->orWhereDate('foods.updated_at', '>=', Carbon::parse($diff_time))->orWhereDate('foods.deleted_at', '>=', Carbon::parse($diff_time));
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
               return $foods->withTrashed()->whereDate('created_at', '>=', Carbon::parse($request->diff_time))->orWhereDate('updated_at', '>=', Carbon::parse($request->diff_time))->orWhereDate('deleted_at', '>=', Carbon::parse($request->diff_time))->get()->pluck('id')->toArray();
            } else {
                 return $foods->get()->pluck('id')->toArray();
            }
        }
    }

    private function categories_check($request, $final_foods)
    {
        if ($request->category_id) {
            $final_foods = $final_foods->where('category_id', $request->category_id);
        } 

        return $final_foods;
    }

    private function paginator($request, $final_foods)
    {
        if ($request->page) {
            $final_foods = $final_foods->paginate($request->per_page);
        } else {
            $final_foods = $final_foods->get();
        }
        return $final_foods;
    }

    private function categories($request, $food_object)
    {
        if ( in_array(__FUNCTION__, $request->with)) {
            if ($food_object->category_id) {
                $food_object->category = $food_object->categories()->first()->categories_trans()->where('language_id', $request->language_id)->with('categories')->first();
            }
        }

        return true;
    }

    private function tags($request, $food_object, $tags_trans)
    {
        if ( in_array(__FUNCTION__, $request->with)) {
            if ($request->diff_time) {
                $tags_ids = $food_object->foods()->withTrashed()->first()->tags()->get()->pluck('id')->toArray();
            } else {
                $tags_ids = $food_object->foods()->first()->tags()->get()->pluck('id')->toArray();
            }
            
            $food_object->tags = $tags_trans->whereIn('tag_id', $tags_ids)->where('language_id', $request->language_id)->with('tags')->get();
        }

        return true;
    }

    private function ingredients($request, $food_object, $ingredients_trans)
    { 
        if (in_array(__FUNCTION__, $request->with)) {
            if ($request->diff_time) {
                $ingredients_ids = $food_object->foods()->withTrashed()->first()->ingredients()->get()->pluck('id')->toArray();
            } else {
                 $ingredients_ids = $food_object->foods()->first()->ingredients()->get()->pluck('id')->toArray();
            }
           
            $food_object->with = $ingredients_trans->whereIn('ingredient_id', $ingredients_ids)->where('language_id', $request->language_id)->with('ingredients')->get();
        }

        return true;
    }

    private function status($request, $food_object)
    {
        if($request->diff_time) {

            $food = $food_object->foods()->withTrashed()->first();

            if ($food->deleted_at) {
                $food_object->status = "deleted";
                return true;
            }

            if ($food->created_at->format('Y-m-d') == $food->updated_at->format('Y-m-d')) {
                $food_object->status = "created";
                return true;
            }

            if ($food->created_at->format('Y-m-d') < $food->updated_at->format('Y-m-d')) {
                $food_object->status = "updated";
                return true;
            }

        } else {
            $food_object->status = "created";
        }
    }

    private function format_response($request, $final_foods, $tags_trans, $ingredients_trans)
    {
        foreach ($final_foods as $food_object) {

            $this->status($request, $food_object);

            $this->categories($request, $food_object);

            $this->tags($request, $food_object, $tags_trans);

            $this->ingredients($request, $food_object, $ingredients_trans);
        
        }
        return response()->json(['data' => $final_foods]);
    }

    
}
