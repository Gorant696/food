<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FoodsTrans extends Model
{
    protected $table = 'foods_trans';
    protected $fillable = ['language_id', 'category_id', 'title', 'description'];

    public function foods()
    {
        return $this->belongsTo('App\Foods', 'food_id');
    }

    public function categories()
    {
        return $this->belongsTo('App\Categories', 'category_id');
    }

    public function languages()
    {
        return $this->belongsTo('App\Languages', 'language_id');
    }
}
