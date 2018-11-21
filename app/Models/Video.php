<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table='videos';

    public function comments()
    {
        return $this->morphMany('App\Models\Comments','status','item_type','item_id','id');
    }

}