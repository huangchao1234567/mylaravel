<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Laravel\Cashier\Billable;

class User extends Model
{
    protected $table='Users';

    public function account()
    {
      return  $this->hasOne('App\Models\UserAccount','user_id','id');
       // return $this->hasOne('App\Models\UserAccount');
    }
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role');
    }


}