<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class TestMiddleware
{
    public function handle($request, Closure $next)
    {
        if($request->input('age')<18)
            return redirect()->route('refuse1');
        return $next($request);
    }
}