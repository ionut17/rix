<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Session;
use Request;
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

    public function get_token()
    {
        $username = Session::get('username');
        $current_date = time();

            //Search the api_token into the DB
        $result = DB::table('api_tokens')->select('token')->where('username',$username)->first();
        if(!empty($result))
        {
            DB::table('api_tokens')->where('username',$username)->delete();
        }

            //Generating unique token with md5
        $base = $username.$current_date;
        $token = md5($base);

            //Storing token to database
        DB::table('api_tokens')->insert(['username'=>$username,'token'=>$token]);
        return $token;
    }

    public function delete_account($token, $service){

    }
    public function connect($token, $service)
    {
        $DBresult = DB::table('api_tokens')->select('username')->where('token',$token)->first();
        $username = $DBresult->username;
        $DBresult = DB::table('accounts')->select('access_token')->where('source_name',$service)->where('username',$username)->first();
        if($DBresult != null)
        {
            echo 'You already have an account on this service';
        }
        else
        {
            Session::put('username',$username);
            switch ($service) {
                case 'slideshare':
                    $slideshare_username = Request::get('slideshare_username');
                    if($slideshare_username!=null)
                    {
                        $slideshare = new SlideshareController();
                        $slideshare->authorize($slideshare_username);
                    }
                    else
                    {
                        echo 'No slideshare username provided';   
                    }
                    break;
                case 'github':
                    $github = new GithubController();
                    $github->authorize();
                    break;
                case 'vimeo':
                    $vimeo = new VimeoController();
                    $vimeo->authorize();
                case 'pocket':
                    $pocket = new PocketController();
                    $pocket->authorize();
                    break;
            }
        }
    }
}
