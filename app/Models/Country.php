<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table='countries';

    public function posts()
    {
        return $this->hasManyThrough('App\Models\Posts','App\User');
    }
}