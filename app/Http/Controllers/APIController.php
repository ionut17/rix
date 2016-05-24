<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Session;
use DB;

class APIController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function call($token, $service){
      $result = DB::table('api_tokens')->select('username')->where('token',$token)->get();

      //Interoghezi baza de date dupa token si gasesti user-ul
      //Si apoi faci call la list-ul aferent serviciului dorit
      if(!empty($result)){
        $content_controller = new ContentController();
        switch($service){
          case 'github':
            $content = $content_controller->listGithub();
            break;
          case 'pocket':
            $content = $content_controller->listPocket();
            break;
          case 'slideshare':
            $content = $content_controller->listSlideshare();
            break;
          case 'vimeo':
            $content = $content_controller->listVimeo();
            break;
          case 'recommend':
            $recommend_controller = new RecommendedController();
            $content = $recommend_controller->recommendVimeo();//.$recommendGithub();
            break;
          default:
            $content = 'Invalid service name.';
            break;
        }
      }
      else {
        $content = 'Invalid token';
      }

      $contentJSON = json_encode($content);
      return $contentJSON;
    }

    public function get_token(){
      $username = Session::get('username');
      $current_date = time();

      //Search the api_token into the DB
      $result = DB::table('api_tokens')->select('token')->where('username',$username)->first();
      if(!empty($result)){
        DB::table('api_tokens')->where('username',$username)->delete();
      }

      //Generating unique token with md5
      $base = $username.$current_date;
      $token = md5($base);

      //Storing token to database
      DB::table('api_tokens')->insert(['username'=>$username,'token'=>$token]);

      return $token;
    }
}
