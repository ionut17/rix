<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PageController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function login(){
    $this->middleware('auth');
      return view('auth.login');
    }

    public function register(){
    $this->middleware('registerauth');
      return view('auth.register');
      // $this->middleware('registerauth');
    }
}
