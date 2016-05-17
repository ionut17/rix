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


class ContentController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(){
      $this->middleware('auth');
    }

    private function listGithub(){
      $client = App::make('Github\Client');
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
      $client = App::make('Github\Client');
      $repos = $client->api('current_user')->repositories();
      $username = env('GIT_USERNAME');
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
      $contentGithub = $this->listGithub();
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
}
