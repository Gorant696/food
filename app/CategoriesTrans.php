<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriesTrans extends Model
{
	protected $table = 'categories_trans';

    public function categories()
    {
    	return $this->belongsTo('App\Categories', 'category_id');
    }

    public function languages()
    {
    	return $this->belongsTo('App\Languages', 'language_id');
    }
}
