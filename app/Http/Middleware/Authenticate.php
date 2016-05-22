<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Request;
use DB;
use Session;  


class Authenticate
{
    public function handle($request, Closure $next, $guard = null)
    {
        if(Session::has('username'))
        {
            return $next($request);
        } 
        else
        {
            if ($request->ajax() || $request->wantsJson())
            {
                return response('Unauthorized.', 401);
            }     
            else
            {
                if(Session::get('error_page')=='register')
                    return redirect()->guest('register')->with('error',Session::get('error'));
                return redirect()->guest('login')->with('error',Session::get('error'));
            }
        }
    }
}