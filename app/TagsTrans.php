<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagsTrans extends Model
{
    protected $table = 'tags_trans';

    public function tags()
    {
    	return $this->belongsTo('App\Tags', 'tag_id');
    }

    public function languages()
    {
    	return $this->belongsTo('App\Languages', 'language_id');
    }
}
