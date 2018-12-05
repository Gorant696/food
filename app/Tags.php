<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    protected $table = 'tags';

    public function tagsTrans()
    {
        return $this->hasMany('App\TagsTrans', 'tag_id');
    }

    public function foods()
    {
    	return $this->belongsToMany('App\Foods', 'foods_tags_pivot', 'tag_id', 'food_id');
    }
}
