<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Request;
use DB;
use Redirect;
use Session;

class AuthController extends Controller
{
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    public function __construct()
    {
       // $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    public function validate_login()
    {
        if(!Session::has('username'))
        {
            Session::flush();
            $username = Request::get('username');
            $password = Request::get('password');

            $DB_password_object = DB::table('users')->select('PASSWORD')->where('username',$username)->get();
            if($DB_password_object!=null)
            {
                $DB_password = $DB_password_object[0]->password;
                if($DB_password==sha1($password))
                {
                    Session::put('username',$username);
                }
                else
                {
                    Session::put('error_page','login');
                    Session::put('error','Bad credentials');
                }
            }
            else
            {
                Session::put('error_page','login');
                Session::put('error','Bad credentials');
            }
        }
        return Redirect::to('mycontent');
    }

    public function validate_register()
    {
        if(!Session::has('username'))
        {
            Session::flush();

            $username = Request::get('username');
            $email = Request::get('email');
            $password = Request::get('password');
            $r_password = Request::get('rpassword');
            if($password != $r_password)
            {
                Session::put('error_page','register');
                Session::put('error',"Passwords don't match.");
            }
            else
            {
                if(strlen($password) < 6)
                {
                    Session::put('error_page','register');
                    Session::put('error','Password is too short.');
                }
                else
                {
                    $DB_username_object = DB::table('users')->select('USERNAME')->where('username',$username)->get();
                    if($DB_username_object!=null)
                    {
                        Session::put('error_page','register');
                        Session::put('error','Username taken.');
                    }
                    else
                    {
                        $DB_email_object = DB::table('users')->select('EMAIL')->where('email',$email)->get();
                        if($DB_email_object!=null)
                        {
                            Session::put('error_page','register');
                            Session::put('error','Email already used.');
                        }
                        else
                        {
                           DB::insert('insert into users (username, password, email) values (?, ?, ?)', array($username, sha1($password), $email));
                           Session::put('username',$username);
                        }
                    }
               }
           }
       }
       return Redirect::to('mycontent');
   }

   public function ajax_validator(){
     $username = Request::input('username');
     $result = DB::table('users')->where('username',$username)->first();
     if(!empty($result)){
       return 'Username already taken';
     }
     $email = Request::input('email');
     if(empty($email)){
       return 'Email not entered';
     }
     $result = DB::table('users')->where('email',$email)->first();
     if(!empty($result)){
       return 'Email already taken';
     }
     $password = Request::input('password');
     $r_password = Request::input('rpassword');
     if($password != $r_password)
     {
       return "Passwords don't match";
     }
     else
     {
       if(strlen($password) < 6)
       {
         return 'Password too short';
       }
     }

     return 'ok';
   }
 }
