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
        $result = DB::select('select account, access_token from accounts where username = ? and source_name = ?', array("admin","github"));
        // dd($result);
        $account = $result[0]->account;
      $token = $result[0]->access_token;
        // $token = Request::input('github_access');
      $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
      $repos = $client->api('current_user')->repositories();
        $account = $repos[0]['owner']['login'];
        //Content
      $username = env('GIT_USERNAME');
      $path = '.';
      $content = null;
      $content = array();
      foreach ($repos as $repo){
          $files = $client->api('repo')->contents()->show($account, $repo['name'], '.');            // dd($repo);
          // $tags = $client->api('repo')->tags($account, $repo['name']);
        foreach( $files as $file){
          if ($file['type']=='file' && $file['size']<1000000){
              // dd($file);
              $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
              // $myfile = $client->api('repo')->contents()->show('ionut17', $repo['name'], $file['path']);
            $file_content['type'] = 'github';
            $file_content['title'] = $file['name'];
            $file_content['repo'] = $repo['name'];
            $file_content['path'] = $file['path'];
              $file_content['tag'] = $extension;
              // $file_content['content'] = base64_decode($myfile['content']);
              // dd($myfile);
            array_push($content,$file_content);
          }
        }
      }
    } catch (\Exception $e) {
        // dd($e->getMessage());
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
      $extension = pathinfo($myfile['name'], PATHINFO_EXTENSION);
    $file_content['type'] = 'github';
    $file_content['name'] = $myfile['name'];
    $file_content['repo'] = $repo;
    $file_content['path'] = $myfile['path'];
      $file_content['tag'] = $extension;
      //Get beautiful code and colors (Hilite API)
      try{
        $beautify_url = 'http://hilite.me/api';
        $beautify_style = 'border:none;border-size:0;padding:0px;border-radius: 0;background: white;';
        $beautify_type = 'default';
        $beautify_data = array('code' => base64_decode($myfile['content']), 'lexer' => $extension, 'style' => $beautify_type, 'divstyles' => $beautify_style);
        $beautify_options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($beautify_data)
            )
        );
        $beautify_context  = stream_context_create($beautify_options);
        $beautify_result = file_get_contents($beautify_url, false, $beautify_context);
        $file_content['content'] = $beautify_result;
      }
      catch (\Exception $e){
        $file_content['content'] = "<code>".base64_decode($myfile['content'])."</code>";
      }
      // dd($beautify_result);

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
        if($value['has_image']==1){
          $file_content['image']=$value['images'][1]['src'];
        }
        // $file_content['image']=$value['images'][1]['src'];
          // if(in_array('images', $value)){
          //   $file_content['images']=$value['images'];
          // }
          // if(in_array('videos', $value)){
          //   $file_content['videos']=$value['videos'];
          // }
        array_push($content,$file_content);
      }
    } catch (\Exception $e) {
      // dd($e->getMessage());
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
      $username ='admin';
      $results = DB::select('select id_article from vimeo_articles where username = ? ', array($username));
      $content = array();

      foreach($results as $result){
        $video = DB::select('SELECT title, description, authors FROM vimeo_articles WHERE id_article =?', array($result->id_article));
        $file_content['type'] = "vimeo";
        $file_content['title'] = $video[0] ->title;
        $file_content['details'] = $video[0] ->authors;
        $file_content['description'] = $video[0]->description;
        array_push($content, $file_content);
      }
 
    }catch (\Exception $e){
      echo $e;
      $content = null;
    }finally{
      return $content;
    }

  }

}



