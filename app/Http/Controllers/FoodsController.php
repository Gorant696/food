<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Foods;
use App\Tags;
use App\FoodsTrans;
use App\TagsTrans;
use App\IngredientsTrans;
use App\Http\Controllers\FoodsRepository;

class FoodsController extends Controller
{
    public function index(
            Request $request,
            Foods $foods,
            Tags $tags,
            FoodsTrans $foods_trans,
            TagsTrans $tags_trans,
            IngredientsTrans $ingredients_trans,
            FoodsRepository $repository
        ) {
       
        //Filter foods by tags
        $final_food_ids = $repository->filterFoodsByTags($request, $tags, $foods);

        //Model of query for collection of foods resource
        $final_foods = $foods_trans->whereIn('food_id', $final_food_ids)->where('language_id', $request->language_id);

        //Modify query based on category_id from request
        $final_foods = $repository->categoriesCheck($request, $final_foods);

        //Paginate collection based on parameters from request
        $final_foods = $repository->paginator($request, $final_foods);

        return $repository->formatResponse($request, $final_foods, $tags_trans, $ingredients_trans);
    }

    /*
        Inserting new food models into foods table
    */
    public function store(FoodsRepository $repository, Request $request, Foods $foods, FoodsTrans $foods_trans)
    {
        $food_model = $repository->store($request, $foods, $foods_trans);

        if ($food_model) {
            return response()->json(['data' => $food_model]);
        } else {
            return response()->json(['message' => 'Food already exists!'], 400);
        }
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
