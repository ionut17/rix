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
use Redirect;

class SettingsController extends BaseController
{

  public function show(){
    $slideshare_error = Session::get('slideshare_error');
    $username = Session::get('username');
    $user_info = DB::select('SELECT username, email, unseen_tutorial FROM users WHERE username=?',array($username));
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
      //tutorial
      $show_tutorial = null;
      if (intval($user_info[0]->unseen_tutorial) == 1){
        $show_tutorial = true;
      }
      else {
        $show_tutorial = false;
      }
      //
    return View::make('settings', ['sources' => $sources, 'user' => $user, 'select_values' => $select_values, 'slideshare_error' => $slideshare_error, 'user_info' => $user_info[0],'show_tutorial'=>$show_tutorial]);
  }

  public function refresh(){
    try{
      $username = Session::get('username');
      $accounts =  DB::table('accounts')->select('source_name')->where('username',$username)->get();
      foreach($accounts as $account)
      {
        switch ($account->source_name) {
          case 'slideshare':
            $slideshare_controller = new SlideshareController();
            $slideshare_controller->authorize();
          break;
          case 'vimeo':
            $access_token = DB::table('accounts')->where('username',$username)->where('source_name','vimeo')->select('access_token')->first();
            $vimeo_controller =  new VimeoController();
            $vimeo_controller->store($access_token->access_token);
          break;
          case 'pocket':
            $pocket_controller = new PocketController();
            $pocket_controller->store();
            break;
          case 'github':
            $github_controller = new GithubController();
            $github_controller->store();
            break;
        }
      }
    }catch(\Exception $e){
      // dd($e);
      //If you don't have an account attached
     return Redirect::to('mycontent');
   }
   return Redirect::to('mycontent');
 }

 public function modify(){
    dd(Request::all());
 }

}
