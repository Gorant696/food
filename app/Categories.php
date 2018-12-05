<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories';

    public function categoriesTrans()
    {
        return $this->hasMany('App\CategoriesTrans', 'category_id');
    }

    public function foodsTrans()
    {
        return $this->hasMany('App\FoodsTrans', 'food_id');
    }

}
