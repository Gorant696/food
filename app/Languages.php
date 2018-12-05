<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Languages extends Model
{
    protected $table = 'languages';

    public function foodsTrans()
    {
        return $this->hasMany('App\FoodsTrans', 'language_id');
    }

    public function categoriesTrans()
    {
        return $this->hasMany('App\CategoriesTrans', 'language_id');
    }

    public function tagsTrans()
    {
        return $this->hasMany('App\TagsTrans', 'language_id');
    }

    public function ingredientsTrans()
    {
        return $this->hasMany('App\IngredientsTrans', 'language_id');
    }
}
