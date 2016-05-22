<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App;
use View;
use Illuminate\Support\Facades\Input;
use Request;
use Duellsy\Pockpack\Pockpack;
use Duellsy\Pockpack\PockpackAuth;
use Duellsy\Pockpack\PockpackQueue;
use Vinkla\Vimeo\Facades\Vimeo;
use Exception;
use DB;


class RecommendedController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  private $max_files_per_api = 30;

  public function __construct(){
      // $this->middleware('auth');
  }

  public function show($page_number=1){
    $contentGithub = null;
    $contentPocket = null;
    $contentSlideshare = null;
    $contentVimeo = null;
      //Content (se adauga un array pentru fiecare API)
    $contentGithub = $this->recommendGithub('website',array('language' => 'html'));
    // $contentPocket = $this->listPocket();
    // $contentVimeo = $this -> listVimeo();

      //Pentru celelalte api-uri se adauga vectorii in array_merge
    $content = null;
      //Adding API's contents
    if ($contentPocket!=null) {
      if ($content != null){
        $content = array_merge($content, $contentPocket);
      }
      else {
        $content = array_merge($contentPocket);
      }
    }
    if ($contentGithub!=null) {
      if ($content != null){
        $content = array_merge($content, $contentGithub);
      }
      else {
        $content = array_merge($contentGithub);
      }
    }
    if ($contentVimeo != null) {
      if ($content != null){
        $content = array_merge($content, $contentVimeo);
      }
      else {
        $content = array_merge($contentVimeo);
      }
    }
    // dd($recommended_files);
    //Get files
    //Settings
    $page_number = intval($page_number);
    $per_page = 8;
    //Pagination
    $article_count = count($content);
    $page_count = intval(ceil($article_count/$per_page));
    $index_start = ($page_number-1)*$per_page;
    //Display content
    $display_content = null;
    if ($content!=null){
        // shuffle($content); //TO REMOVE NOT SHUFFLING CORECTLY
      $display_content = array_slice($content,$index_start,$per_page);
    }
    return View::make('content', ['content' => $display_content,'page_count'=>$page_count,'page_number'=>$page_number, 'target'=>'recommended']);
  }

  //API's recommendations

  public function recommendGithub($search_input,$languages){
    try{
      $client = new \Github\Client();
      $rix_username = Session::get('username');
      $result = DB::select('select access_token from accounts where username = ? and source_name = ?', array($rix_username,"github"));
      $token = $result[0]->access_token;
      $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
      //Get repos
      $repos = $client->api('repo')->find($search_input, $languages);
      $recommended_files = array();
      $count = 0;
      $counted_files = 0;
      while ($counted_files<$this->max_files_per_api){
        $count = $count + 1;
        $files = $client->api('repo')->contents()->show($repos['repositories'][$count]['username'], $repos['repositories'][$count]['name'], '.');
        foreach ($files as $file){
          if ($counted_files > $this->max_files_per_api){
            break;
          }
          if ($file['type']=='file' && $file['size']<1000000){
            // dd($file);
              $file_content['type'] = 'github';
              $file_content['title'] = $file['name'];
              $file_content['repo'] = $repos['repositories'][$count]['name'];
              $file_content['path'] = $file['path'];
              $file_content['details'] = $repos['repositories'][$count]['username'].'\\'.$repos['repositories'][$count]['name'].'\\'.$file['path'];
              $file_content['username'] = $repos['repositories'][$count]['username'];
              // $file_content['path'] = $file['path'];
              $file_content['tag'] = pathinfo($file['name'], PATHINFO_EXTENSION);
              // $file_content['username'] = $repos['repositories'][$count]['username'];
              if (isset($file['description'])){
                $file_content['description'] = $file['description'];
              }
              $counted_files = $counted_files + 1;
              // $file_content['content'] = base64_decode($myfile['content']);
              array_push($recommended_files, $file_content);
          }
        }
      }
    }
    catch (\Exception $e){
      // dd($e->getMessage());
      $recommended_files = null;
    }
    finally{
      return $recommended_files;
    }
  }

  public function article($type,$api){
    $repo = Request::input('repo');
    $path = Request::input('path');
    if ($type=='image'){
      return view('articles.image-article');
    }
    if ($type=='video'){
      return view('articles.video-article');
    }
    if ($type=='code'){
      if ($api == 'github'){
        $article = $this->contentGithub($repo,$path);
      }
        // dd($article);
      return View::make('articles.code-article',['content'=>$article]);
    }
    return view('layouts.article');
  }

}
