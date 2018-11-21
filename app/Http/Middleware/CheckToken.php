<?php
namespace App\Http\Middleware;

use Closure;

class CheckToken
{
    public function handle($request,Closure $next)
    {
        $token  =$request->input('token');
        $auth2  =$request->input('auth2');
        if ($token=='laravelacadeny.org'&&$auth2=='12'){
            return redirect()->to('http://laravelacadeny.org');
        }
        return $next($request);
    }
}