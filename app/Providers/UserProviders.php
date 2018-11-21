<?php
namespace App\Providers;

use App\Models\User;

class UserProviders
{
    public static function User()
    {
        return User::where('id','>',0)->get();
    }
}