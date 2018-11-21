<?php
namespace App\Http\Controllers\Trademarks;


class Login
{
    public function show($id=1)
    {
        return view('user.profile', ['user' => $id]);
    }

}