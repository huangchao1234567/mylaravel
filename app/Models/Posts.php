<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    protected $table    ='posts';

    public function scopePopular($query)
    {
        return $query->where('views','>=',100);
    }

    public function scopeStatus($query,$status=1)
    {
        return $query->where('status',$status);
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comments','item');
    }
}