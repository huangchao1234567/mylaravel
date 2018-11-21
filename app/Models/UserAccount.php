<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
   protected $table='user_accounts';

    public function user()
    {

       // return $this->belongsTo('App\User');
    }
}
