<?php
namespace Illuminate\Http\Concerns;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserInfoController extends Controller
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
        return view('user.profile', ['user' => 1]);
    }

}