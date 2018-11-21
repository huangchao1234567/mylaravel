<?php
namespace  App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Providers\User\UserProviders;
use Redis;

class UserController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->user= new UserProviders();
    }

    public function index(Request $request)
    {
        $param  = [
             'num'  =>  $request->input('num',20),
             'page' =>  $request->input('page',1),
            ];
      //  dd($param);
        $data= $this->user->User($param);
        return view('user.user',$data);
    }

    public function number()
    {
        $str    ='卡萨丁123';
        $length =mb_strlen($str);
        for ($i=0;$i<$length;++$i){

        }

    }

}