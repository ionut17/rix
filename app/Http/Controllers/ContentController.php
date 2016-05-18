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


class ContentController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(){
      // $this->middleware('auth');
    }

    private function listGithub(){
      //Connection
      $client = new \Github\Client();
      $token = Request::input('github_access');
      $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
      //Content
      $repos = $client->api('current_user')->repositories();
      $username = env('GIT_USERNAME');
      $path = '.';
      $content = null;
      $content = array();
      foreach ($repos as $repo){
        $files = $client->api('repo')->contents()->show('ionut17', $repo['name'], '.');
        // dd($files);
        foreach( $files as $file){
          if ($file['type']=='file' && $file['size']<1000000){
            // dd($file);
            // $myfile = $client->api('repo')->contents()->show('ionut17', $repo['name'], $file['path']);
            $file_content['type'] = 'github';
            $file_content['name'] = $file['name'];
            $file_content['repo'] = $repo['name'];
            $file_content['path'] = $file['path'];
            // $file_content['content'] = base64_decode($myfile['content']);
            // dd($myfile);
            array_push($content,$file_content);
          }
        }
      }
      return $content;
    }

    private function contentGithub($repo, $path){
      //Connection
      $client = new \Github\Client();
      $token = Request::input('github_access');
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

    public function show($page_number=1){
      //content (se adauga un array pentru fiecare api)
      $contentGithub = [''];
      // $contentGithub = $this->listGithub();
      //pentru celelalte api-uri se adauga vectorii in array_merge
      $content = array_merge($contentGithub);
      //settings
      $page_number = intval($page_number);
      $per_page = 8;
      //pagination
      $article_count = count($content);
      $page_count = intval(ceil($article_count/$per_page));
      $index_start = ($page_number-1)*$per_page;
      $display_content = array_slice($content,$index_start,$per_page);
      //view
      $display_content = null;
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

    //Pocket
    public function firstConnectPocket(){
      $consumer_key="54303-44cfeebb44dcc915b80532df";
      $pockpath_auth = new PockpackAuth();
      // echo "Created authentication<br>";
      $request_token = $pockpath_auth->connect($consumer_key);
      // echo "Created request token<br>";
      echo $request_token;
      header("Location: https://getpocket.com/auth/authorize?request_token=".$request_token."&redirect_uri=http://localhost:3000/activate/pocket?request_token=".$request_token);
      exit();
    }
//IMPORTANT: for DB: access token and consumer key
    public function listPocket(){
      $consumer_key="54303-44cfeebb44dcc915b80532df";
      $pockpath_auth = new PockpackAuth();
      $local_request_token=Request::input('request_token');
      // dd($this->consumer_key,$local_request_token);
      // echo "Consumer key: ".$consumer_key."<br>";
      // echo "Request key: ".$local_request_token."<br>";
      //Access token
      $access_token = $pockpath_auth->receiveTokenAndUsername($consumer_key, $local_request_token);
      // dd($access_token['access_token']);
      // echo "Created acces token<br>";
      // echo "Access token: ".$access_token['access_token']."<br>";
      //Interogating
      $options = array(
          'state'         => 'all',
          'contentType'   => 'article',
          'detailType'    => 'complete'
      );
      // echo "Interogating list<br>";
      $pockpack = new Pockpack($consumer_key, $access_token['access_token']);
    // echo "Retrieving list<br>";
    $array = $pockpack->retrieve($options,1);
    $articles_list = $array['list'];

      // dd($list);
      $content = null;
      $content = array();
      foreach($articles_list as $value){
        $file_content['title']=$value['resolved_title'];
        $file_content['path']=$value['resolved_url'];
        $file_content['excerpt']=$value['excerpt'];
        if(in_array('images', $value)){
          $file_content['images']=$value['images'];
        }
        // if(in_array('videos', $value)){
        //   $file_content['videos']=$value['videos'];
        // }

        array_push($content,$file_content);
      }
      echo 'Content: ';
      dd($content);
    }

}
