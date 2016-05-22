<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use View;
use DB;

class SettingsController extends BaseController
{

    public function show(){
      $results = DB::select('SELECT source_name FROM accounts WHERE username=?',array('admin'));
      $sources = array();
      foreach ($results as $result){
        array_push($sources, $result->source_name);
      }
      // dd($sources);
      return View::make('settings', ['sources' => $sources]);
    }

}
