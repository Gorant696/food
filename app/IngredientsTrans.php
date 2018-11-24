<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IngredientsTrans extends Model
{
	protected $table = 'ingredients_trans';

    public function languages()
    {
    	return $this->belongsTo('App\Languages', 'language_id');
    }

    public function ingredients()
    {
    	return $this->belongsTo('App\Ingredients', 'ingredient_id');
    }
}
