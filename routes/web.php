<?php
/*Route::resource('test','TestController');
Route::resource('user','Test\UserController');


// 认证路由...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');
// 注册路由...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');*/

Route::get('/', function ()    {
    return view('user.greeting', ['name' => 'James1']);
});

Route::get('user/{id}', 'UserController@show');
/*Route::group(['namespace'=>'User'], function () {
    Route::get('index', 'User/UserController@index');
});*/
Route::get('user/input','UserController@input');
//
Route::get('testCsrf',function(){
    $csrf_field = csrf_field();
    $html = <<<GET
        <form method="POST" action="/testCsrf">
            {$csrf_field}
            <input type="submit" value="Test"/>
        </form>
GET;
    return $html;
});

Route::post('testCsrf',function(){
    return 'Success!';
});

Route::get('testCsrf',function(){
    $html = <<<GET
        <form method="POST" action="/testCsrf">
            <input type="submit" value="Test"/>
        </form>
GET;
    return $html;
});