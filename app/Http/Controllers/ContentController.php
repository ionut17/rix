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


class ContentController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  public function __construct(){
      // $this->middleware('auth');
  }

  public function show($page_number=1){
    $contentGithub = null;
    $contentPocket = null;
    $contentSlideshare = null;
    $contentVimeo = null;

      //Content (se adauga un array pentru fiecare API)
    $contentGithub = $this->listGithub();
    $contentPocket = $this->listPocket();
    $contentVimeo = $this -> listVimeo();

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
      //View
    return View::make('content', ['content' => $display_content,'page_count'=>$page_count,'page_number'=>$page_number]);
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

    //APIs

    //GITHUB
    //List github articles
  private function listGithub(){
      //Connection
    $client = new \Github\Client();
    try {
      $result = DB::select('select access_token from accounts where username = ? and source_name = ?', array("admin","github"));
      $token = $result[0]->access_token;
        // $token = Request::input('github_access');
      $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
      $repos = $client->api('current_user')->repositories();
        //Content
      $username = env('GIT_USERNAME');
      $path = '.';
      $content = null;
      $content = array();
      foreach ($repos as $repo){
        $files = $client->api('repo')->contents()->show('EvelineAndreea', $repo['name'], '.');
          // dd($files);
        foreach( $files as $file){
          if ($file['type']=='file' && $file['size']<1000000){
              // dd($file);
              // $myfile = $client->api('repo')->contents()->show('ionut17', $repo['name'], $file['path']);
            $file_content['type'] = 'github';
            $file_content['title'] = $file['name'];
            $file_content['repo'] = $repo['name'];
            $file_content['path'] = $file['path'];
              // $file_content['content'] = base64_decode($myfile['content']);
              // dd($myfile);
            array_push($content,$file_content);
          }
        }
      }
    } catch (\Exception $e) {
        // dd("I have errors");
      $content = null;
    } finally {
      return $content;
    }
  }

    //Get content of github article
  private function contentGithub($repo, $path){
      //Connection
    $client = new \Github\Client();
    $result = DB::select('select access_token from accounts where username = ?', array("admin"));
    $token = $result[0]->access_token;
    $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
      //Content
    $repos = $client->api('current_user')->repositories();
    $content = array();
    $myfile = $client->api('repo')->contents()->show('ionut17', $repo, $path);
      // dd($myfile);
    $file_content['type'] = 'github';
    $file_content['name'] = $myfile['name'];
    $file_content['repo'] = $repo;
    $file_content['content'] = base64_decode($myfile['content']);
    $file_content['path'] = $myfile['path'];
      // dd($file_content);
    return $file_content;
  }


    //POCKET
    //List pocket articles
  public function listPocket(){
      //Getting the consumer key and user key
    $consumer_key=env('POCKET_CONSUMER_KEY');
    try{
      $result = DB::select('select access_token from accounts where username = ? and source_name = ?', array("admin","pocket"));
      $access_token = $result[0]->access_token;
        //Making connection
      $pockpack = new Pockpack($consumer_key, $access_token);
        //Setting the options
      $options = array(
        'state'         => 'all',
        'contentType'   => 'article',
        'detailType'    => 'complete'
        );
      $array = $pockpack->retrieve($options,1);
      $articles_list = $array['list'];
      $content = null;
      $content = array();
      foreach($articles_list as $value){
        $file_content['type']= "pocket";
        $file_content['title']=$value['resolved_title'];
        $file_content['path']=$value['resolved_url'];
        $file_content['description']=$value['excerpt'];
        $file_content['image']=$value['images'][1]['src'];
          // if(in_array('images', $value)){
          //   $file_content['images']=$value['images'];
          // }
          // if(in_array('videos', $value)){
          //   $file_content['videos']=$value['videos'];
          // }
        array_push($content,$file_content);
      }
    } catch (\Exception $e) {
      $content = null;
    } finally {
      return $content;
    }
  }

    //Get content of pocket article
  private function contentPocket(){

  }

  //Make Vimeo articles

  public function listVimeo(){
    try{
      $result = DB::select('select access_token from accounts where username = ? and source_name = ?', array("admin","vimeo"));
      $access_token = $result[0]->access_token;

      $vimeo_connection = Vimeo::connection('alternative');
      $vimeo_connection -> setToken($access_token);
      $response = $vimeo_connection -> request('/me/videos', [], 'GET');

      $content = null;
      $content = array();
      $videos = $response['body']['data'];
    // dd($videos);
      foreach($videos as $video){
        $file_content['type'] = "vimeo";
        $file_content['title'] = $video['name'];
        $file_content['description'] = $video['description'];
        array_push($content, $file_content);
      }
    }catch (\Exception $e){
      $content = null;
    }finally{
      return $content;
    }

  }

}

