<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Request;
use DB;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
      $username = Request::get('username');
      $password = Request::get('password');

      $user = DB::table('users')->where('username',$username)->first();

      if(isset($user))
      {
        return $next($request);

      } else {
        if ($request->ajax() || $request->wantsJson()) {
            return response('Unauthorized.', 401);
        } else {
            return redirect()->guest('login');
        }
      }

    }
}
