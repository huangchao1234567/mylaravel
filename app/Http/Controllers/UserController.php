<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use PhpConsole\Handler;

class UserController extends Controller
{
    /**
     * 为指定用户显示详情
     *
     * @param int $id
     * @return Response
     * @author LaravelAcademy.org
     */
    public function show($id)
    {
        Handler::getInstance()->debug($id);//直接线上测试
        return view('user.profile', ['user' => $id]);
    }
    public function input()
    {
        return view('user.input');
    }
}
