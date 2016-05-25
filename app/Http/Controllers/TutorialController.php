<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DB;
use Session;
use Redirect;

class TutorialController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function show(){
      $rix_username = Session::get('username');
      $result = DB::update('update users set unseen_tutorial=1 where username = ?', array($rix_username));
      return redirect('mycontent');
    }

    public function hide(){
      $rix_username = Session::get('username');
      $result = DB::update('update users set unseen_tutorial=0 where username = ?', array($rix_username));
      return 'hiding tutorial';
    }
}
