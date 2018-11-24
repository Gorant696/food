<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories';

    public function categories_trans()
    {
        return $this->hasMany('App\CategoriesTrans', 'category_id');
    }

    public function foods_trans()
    {
        return $this->hasMany('App\FoodsTrans', 'food_id');
    }

}
