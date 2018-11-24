<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Languages extends Model
{
    protected $table = 'languages';

    public function foods_trans()
    {
        return $this->hasMany('App\FoodsTrans', 'language_id');
    }

    public function categories_trans()
    {
        return $this->hasMany('App\CategoriesTrans', 'language_id');
    }

    public function tags_trans()
    {
        return $this->hasMany('App\TagsTrans', 'language_id');
    }

    public function ingredients_trans()
    {
        return $this->hasMany('App\IngredientsTrans', 'language_id');
    }
}
