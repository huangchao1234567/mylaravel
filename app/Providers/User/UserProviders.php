<?php
namespace App\Providers\User;

use App\Models\User;

class UserProviders
{
    public static function User($param)
    {
        return User::where('id','>',0)->paginate($param['num']);
    }
}