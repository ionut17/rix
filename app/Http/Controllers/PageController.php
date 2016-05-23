<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Request;
use Redirect;
use DB;
use Session;

class PageController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function logout()
    {
        Session::flush();
        return Redirect::to('login');
    }

    public function login(){
        if(Session::has('username'))
            return Redirect::to('mycontent');
        return view('auth.login');
    }

    public function register(){
        $this->middleware('registerauth');
        return view('auth.register');
      // $this->middleware('registerauth');
    }

    public function authorizeAPI(){
        $type = Request::input('api');
        switch ($type) 
        {
            case 'github':
                //Requesting authorization
            header('Location: http://localhost:2000/authorize/github');
            exit();
            break;
            case 'pocket':
                //Requesting authorization
            header('Location: http://localhost:2000/authorize/pocket');
            exit();
            break;
            case 'slideshare':
            header('Location: http://localhost:2000/authorize/slideshare/?slideshare_username='.Request::get('slideshare_username'));
            exit();
            break;
            case 'vimeo':
            header('Location: http://localhost:2000/authorize/vimeo');
            exit();
            break;
            default:
            dd('Invalid account type');
        }
    }

    public function removeAPI($api){
        $username = Session::get('username');
        $result = DB::statement('delete from accounts where username = ? and source_name = ?', array($username,$api));
        return redirect('settings');
  }
}
