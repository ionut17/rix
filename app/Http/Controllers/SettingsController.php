<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use View;
use DB;
use Session;
use Request;

class SettingsController extends BaseController
{

    public function show(){
      $slideshare_error = Session::get('slideshare_error');
      $username = Session::get('username');
      $user_info = DB::select('SELECT username, email FROM users WHERE username=?',array($username));
      $dir = getcwd();
      $filename = $dir.'\img\profiles\\'.$user_info[0]->username.'.jpg';
      if (file_exists($filename)) {
        $user_info[0]->avatar= $user_info[0]->username;
      } else {
        $user_info[0]->avatar='default';
      }
      //Getting sources
      $results = DB::select('SELECT source_name FROM accounts WHERE username=?',array($username));
      $sources = array();
      $select_values =  array('github', 'pocket', 'slideshare', 'vimeo');
      foreach ($results as $result){
        array_push($sources, $result->source_name);
        if (($key = array_search($result->source_name, $select_values)) !== false) {
            unset($select_values[$key]);
        }
      }
      $user = Session::get('username');
      // dd($sources);
      return View::make('settings', ['sources' => $sources, 'user' => $user, 'select_values' => $select_values, 'slideshare_error' => $slideshare_error, 'user_info' => $user_info[0]]);
    }

    public function modify(){

    }

}
