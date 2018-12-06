<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Foods extends Model
{
    use SoftDeletes;

    protected $fillable = ['slug'];
    
    protected $table = 'foods';

    public function foodsTrans()
    {
        return $this->hasMany('App\FoodsTrans', 'food_id');
    }

    public function ingredients()
    {
        return $this->belongsToMany('App\Ingredients', 'foods_ingredients_pivot', 'food_id', 'ingredient_id');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tags', 'foods_tags_pivot', 'food_id', 'tag_id');
    }
}
