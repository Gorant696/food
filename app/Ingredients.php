<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingredients extends Model
{
    protected $table = 'ingredients';

    public function ingredientsTrans()
    {
        return $this->hasMany('App\IngredientsTrans', 'ingredient_id');
    }

    public function foods()
    {
        return $this->belongsToMany('App\Foods', 'foods_ingredients_pivot', 'ingredient_id', 'food_id');
    }
}
