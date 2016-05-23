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
use Session;
use DB;

class ContentController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  public function __construct(){
    $this->middleware('auth');
  }

  public function buildContent(){
    //Verify if user has any accounts
    $rix_username = Session::get('username');
    $result = DB::table('accounts')->where('username', '=', $rix_username)->count();
    //Pentru celelalte api-uri se adauga vectorii in array_merge
    $content = null;
    if ($result != 0){
      Session::put('has_accounts',true);
    //Get content
      $contentGithub = null;
      $contentPocket = null;
      $contentSlideshare = null;
      $contentVimeo = null;

    //Content (se adauga un array pentru fiecare API)
      $contentGithub = $this->listGithub();
      $contentPocket = $this->listPocket();
      $contentVimeo = $this->listVimeo();
      $contentSlideshare = $this->listSlideshare();

    //Adding API's contents
      //Adding filters to the displayed articles

      // $filter = Session::get('filter');

      $filter=array();
      $filter['github']=1;
      $filter['pocket']=1;
      $filter['slideshare']=1;
      $filter['vimeo']=1;

      if($filter['pocket']==1){
        if ($contentPocket!=null) {
          if ($content != null){
            $content = array_merge($content, $contentPocket);
          }
          else {
            $content = array_merge($contentPocket);
          }
        }
      }
      if($filter['github']==1){
        if ($contentGithub!=null) {
          if ($content != null){
            $content = array_merge($content, $contentGithub);
          }
          else {
            $content = array_merge($contentGithub);
          }
        }
      }
      if($filter['vimeo']==1){
        if ($contentVimeo != null) {
          if ($content != null){
            $content = array_merge($content, $contentVimeo);
          }
          else {
            $content = array_merge($contentVimeo);
          }
        }
      }
      if($filter['slideshare']==1){
        if ($contentSlideshare != null) {
          if ($content != null){
            $content = array_merge($content, $contentSlideshare);
          }
          else {
            $content = array_merge($contentSlideshare);
          }
        }
      }
      if ($content == null){
        Session::put('content',$content);
        Session::save();
        return $this->show();
      }
      else{
        shuffle($content);
        Session::put('content',$content);
        Session::save();
        return $this->show();
      }
    }
    else{
     Session::put('content', null);
     Session::put('has_accounts', false);
     Session::save();
     return $this->show();
   }
 }

 public function show($page_number=1){
  $content = Session::get('content');
  $has_accounts = Session::get('has_accounts');
    // dd($has_accounts);
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
  $select_values =  array('github', 'pocket', 'slideshare', 'vimeo');
    //View
    // dd($has_accounts, $display_content, $page_count, $page_number, $select_values);
  return View::make('content', ['has_accounts' => $has_accounts,'content' => $display_content,'page_count'=>$page_count,'page_number'=>$page_number,'select_values'=>$select_values]);
}

public function article($type,$api){
  if ($type=='image'){
    if($api == 'pocket'){
      $article_id = Request::input('id');
      $article = $this->contentPocket($article_id);
      return View::make('articles.image-article',['content'=>$article]);
    }
  }
  if ($type=='video'){
    if ($api =='vimeo'){
      $id = Request::input('id');
      $tag = Request::input('tag');
      $article = $this->contentVimeo($id, $tag);
      return View::make('articles.video-article',['content'=>$article]);
    }
    if ($api == 'pocket'){
      $article_id = Request::input('id');
      $article = $this->contentPocket($article_id);
      return View::make('articles.video-article',['content'=>$article]);
    }
      // return view('articles.video-article');
  }
  if ($type=='code'){
    if ($api == 'github'){
      $id = Request::input('id');
      $username = Request::input('username');
      $repo = Request::input('repo');
      $path = Request::input('path');
      if ($repo && $path){
        $article = $this->contentGithub($id, $username, $repo, $path);
      }
      else{
        $article = $this->contentGithub($id);
      }
      return View::make('articles.code-article',['content'=>$article]);
    }
        // dd($article);
  }
  return view('layouts.article');
}

    //APIs

    //GITHUB
    //List github articles
private function listGithub(){
      //Connection
      // dd('entered');
  $client = new \Github\Client();
  try {
      //Content
    $rix_username = Session::get("username");
    $results = DB::select('SELECT id_article FROM github_articles WHERE username=?',array($rix_username));
      // dd($results);
    $content = array();
    foreach ($results as $result){
        //Selecting values from db
      $values=DB::select('SELECT id_article, title, repo, path, extension FROM github_articles WHERE id_article = ?', array($result->id_article));
        //Saving values to object
      $file_content['type']='github';
      $file_content['id'] = $values[0]->id_article;
      $file_content['title'] = $values[0]->title;
      $file_content['details'] = $values[0]->repo.'\\'.$values[0]->path;
      $file_content['tag'] = $values[0]->extension;
      $file_content['username'] = '';
        //Pushing object to array
      array_push($content,$file_content);
    }
      // dd($content);
  } catch (\Exception $e) {
        // dd($e->getMessage());
    $content = null;
  } finally {
    return $content;
  }
}

    //Get content of github article
private function contentGithub($id, $username='', $g_repo='', $g_path=''){
  $rix_username = Session::get("username");
    //Getting values
  try{
      //Connection
    $client = new \Github\Client();
    $result = DB::select('select access_token from accounts where username = ? and source_name = ?', array($rix_username,"github"));
    $token = $result[0]->access_token;
    $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
      // Content
    if ($username!=''){
      $repos = $client->api('user')->repositories($username);
      $repo = $g_repo;
      $path = $g_path;
      $account = $username;
    }
    else {
      $result = DB::select('SELECT repo, path FROM github_articles WHERE id_article = ?', array($id));
      $repo = $result[0]->repo;
      $path = $result[0]->path;
      $repos = $client->api('current_user')->repositories();
      $account = $repos[0]['owner']['login'];
    }
    $content = array();
    $myfile = $client->api('repo')->contents()->show($account, $repo, $path);
        // dd($myfile);
    $extension = pathinfo($myfile['name'], PATHINFO_EXTENSION);
    $file_content['type'] = 'github';
    $file_content['title'] = $myfile['name'];
    $file_content['details'] = $repo.'/'.$myfile['path'];
        // $file_content['path'] = $myfile['path'];
    $file_content['tag'] = $extension;
    $file_content['url'] = $myfile['html_url'];
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
  }
  catch (\Exception $e){
    $file_content = null;
  }
  return $file_content;
}


    //POCKET
    //List pocket articles
public function listPocket(){
  try{
    $rix_username = Session::get("username");
    $results = DB::select('SELECT id_article FROM pocket_articles WHERE username=?',array($rix_username));
    $content = array();
    foreach ($results as $result){
        //Selecting values from db
      $values=DB::select('SELECT title, url_content, description, image_url, video_url FROM pocket_articles WHERE id_article = ?', array($result->id_article));
        // dd($values);
      $file_content['type']='pocket';
      $file_content['id'] = $result->id_article;
      $file_content['title'] = $values[0]->title;
      $file_content['url_content'] = $values[0]->url_content;
      $file_content['description'] = $values[0]->description;
      $file_content['image'] = $values[0]->image_url;
      $file_content['video'] = $values[0]->video_url;

      array_push($content,$file_content);
    }
      // dd($content);
  } catch (\Exception $e) {
        // dd($e->getMessage());
    $content = null;
  } finally {
    return $content;
  }
}

    //Get content of pocket article
private function contentPocket($id){

  $rix_username = Session::get("username");
        //Selecting values from db
  $values=DB::select('SELECT title, url_content, description, image_url, video_url, authors FROM pocket_articles WHERE id_article = ?', array($id));
        // dd($values);
  $file_content = array();

  $file_content['type']='pocket';
  $file_content['id'] = $id;
  $file_content['title'] = $values[0]->title;
  $file_content['url_content'] = $values[0]->url_content;
  $file_content['description'] = $values[0]->description;
  $file_content['image'] = $values[0]->image_url;
  $file_content['video'] = $values[0]->video_url;

        //Breaking authors string into array;
  $authors_string = $values[0]->authors;
  $author_details = array();
  $authors = array();

  $authors_strings = explode(" | ", $authors_string);
  foreach($authors_strings as $string){
    if($string!=null){
      $author_details = explode(" - ", $string);
      array_push($authors, $author_details);
    }
  }
  $file_content['authors'] = $authors;
  return $file_content;
}

  //Make Vimeo articles
public function listVimeo(){
  $content = array();
  try{
    $username = Session::get('username');
    $results = DB::select('select id_article from vimeo_articles where username = ? ', array($username));
    foreach($results as $result){
      $video = DB::select('SELECT id_article, title, description, authors FROM vimeo_articles WHERE id_article =?', array($result->id_article));
      $file_content['type'] = 'vimeo';
      $file_content['title'] = $video[0] ->title;
      $file_content['details'] = $video[0] ->authors;
      $file_content['description'] = $video[0]->description;
      $file_content['id'] = $video[0]->id_article;
      array_push($content, $file_content);
    }
  }catch (\Exception $e){
    $content = null;
  }finally{
    return $content;
  }
}

public function contentVimeo($id, $tag){
  try{
    $username = Session::get('username');
    $result = DB::table('accounts')->select('access_token')->where('username','=',$username)->where('source_name','=','vimeo')->first();
    $access_token = $result->access_token;
    try{
      //verify if content is in database
      $result_video = DB::table('vimeo_articles')->select('url_content')->where('id_article','=',$id)->first();
      $vimeo_connection= Vimeo::connection('alternative');
      $vimeo_connection->setToken($access_token);
      $article = $vimeo_connection->request($result_video->url_content,[],'GET');
    }catch(\Exception $e){
      //catch invalid_number exception from db => that means it's a recommended content
      $vimeo_connection =Vimeo::connection('main');
      $article = $vimeo_connection->request($id,[],'GET');
    }
    //construct file_content object to visualize
    $file_content['type'] = 'vimeo';
    $file_content['title'] = $article['body']['name'];
    $file_content['description'] = $article['body']['description'];
    $file_content['details'] = 'Author:  '.$article['body']['user']['name'].'<br/> Tag: '.$tag;
    $file_content['content'] = $article['body']['embed']['html'];
    $file_content['url'] = $article['body']['link'];
    return $file_content;
  }catch(\Exception $e){
    $file_content = null;
  }finally{
    return $file_content;
  }
}
  //Slideshare

public function listSlideshare()
{
  $content = array();
  try{
    $username = Session::get('username');
    $results = DB::select('select id_article from slideshare_articles where username = ?', array($username));
    foreach($results as $result){
      $slideshow = DB::select('SELECT id_article, author, title, description, image_url FROM slideshare_articles WHERE id_article =?', array($result->id_article));
      $file_content = array();
      $file_content['type'] = 'slideshare';
      $file_content['id'] = $slideshow[0]->id_article;
      $file_content['title'] = $slideshow[0] ->title;
      $file_content['details'] = $slideshow[0] ->author;
      $file_content['description'] = $slideshow[0]->description;
      $file_content['image'] = $slideshow[0]->image_url;

      array_push($content,$file_content);
    }
  }catch (\Exception $e){
    $content = null;
  }finally{
    return $content;
  }
}


public function contentSlideshare($id)
{

    }

    //function used for search
    public function search(){
      $search_string = Request::input('search_string');
      $all_results = array();

      $modified = '%'.$search_string.'%';

      //Search for Pocket articles
      $pocket_results = DB::select("SELECT id_article, title, image_url, video_url FROM pocket_articles WHERE upper(title) like upper(?)", array($modified));
      foreach($pocket_results as $result){
        $article = array();
        $article['id'] = $result->id_article;
        $article['title'] = $result->title;
        $article['type'] = 'pocket';
        //Constructing the article's route
        $article['url']='/article/';
        if($result->video_url!=null)
          $article['url'].='video/pocket?id=';
        else
          $article['url'].='image/pocket?id=';
        $article['url'].=$result->id_article;
        array_push($all_results,$article);
      }

      //Search for Github articles
      $github_results = DB::select("SELECT id_article, title FROM github_articles WHERE upper(title) like upper(?)", array($modified));
      foreach($github_results as $result){
        $article = array();
        $article['id'] = $result->id_article;
        $article['title'] = $result->title;
        $article['type'] = 'github';
        //Constructing the article's route
        $article['url']='/article/code/github?id=';
        $article['url'].=$result->id_article;
        array_push($all_results,$article);
      }

      //Search for Slideshare articles
      $slideshare_results = DB::select("SELECT id_article, title FROM slideshare_articles WHERE upper(title) like upper(?)", array($modified));
      foreach($slideshare_results as $result){
        $article = array();
        $article['id'] = $result->id_article;
        $article['title'] = $result->title;
        $article['type'] = 'slideshare';
        //Constructing the article's route
        $article['url']='/article/video/slideshare?id=';
        $article['url'].=$result->id_article;
        array_push($all_results,$article);
      }

      //Search for Vimeo articles
      $vimeo_results = DB::select("SELECT id_article, title FROM vimeo_articles WHERE upper(title) like upper(?)", array($modified));
      foreach($vimeo_results as $result){
        $article = array();
        $article['id'] = $result->id_article;
        $article['title'] = $result->title;
        $article['type'] = 'vimeo';
        //Constructing the article's route
        $article['url']='/article/video/vimeo?id=';
        $article['url'].=$result->id_article;
        array_push($all_results,$article);
      }

      return $all_results;
    }
}
