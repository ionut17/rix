<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use View;
use DB;
use Session;

class SettingsController extends BaseController
{

    public function show(){
      $slideshare_error = Request::input('slideshare_error');
      $results = DB::select('SELECT source_name FROM accounts WHERE username=?',array('admin'));
      $sources = array();
      foreach ($results as $result){
        array_push($sources, $result->source_name);
      }
      $user = Session::get('username');
      // dd($sources);
      return View::make('settings', ['sources' => $sources, 'user' => $user]);
    }

}
