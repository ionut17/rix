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

      $filter=array();
      $filter['github']=1;
      $filter['pocket']=1;
      $filter['slideshare']=1;
      $filter['vimeo']=1;

      $filter_session = Session::get('filter');
      if (isset($filter_session)){
        if (isset($filter_session['github'])){
          $filter['github'] = $filter_session['github'];
        }
        if (isset($filter_session['pocket'])){
          $filter['pocket'] = $filter_session['pocket'];
        }
        if (isset($filter_session['slideshare'])){
          $filter['slideshare'] = $filter_session['slideshare'];
        }
        if (isset($filter_session['vimeo'])){
          $filter['vimeo'] = $filter_session['vimeo'];
        }
      }
      else{
        Session::put('filter', $filter);
        Session::save();
      }

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
  //get tutorial status
  $username = Session::get('username');
  $user_info = DB::select('SELECT unseen_tutorial FROM users WHERE username=?',array($username));
  $show_tutorial = null;
  if (intval($user_info[0]->unseen_tutorial) == 1){
    $show_tutorial = true;
  }
  else {
    $show_tutorial = false;
  }
  //return
  return View::make('content', ['has_accounts' => $has_accounts,'content' => $display_content,'page_count'=>$page_count,'page_number'=>$page_number,'select_values'=>$select_values,'show_tutorial'=>$show_tutorial]);
}

public function article($type,$api){
  //Choosing two articles to display
  $recommended = array();
  $content = Session::get('content');
  if(!empty($content)){
  //   type" => "pocket"
  // "id" => "1023"
  // "title" => "What disturbed me about the Facebook meeting."
  // "url_content" => "https://medium.com/@glennbeck/what-disturbed-me-about-the-facebook-meeting-3bbe0b96b87f#.43b5x0gcn"
  // "description" => "Yesterday, I had an opportunity to meet with some of the senior staff at Facebook, including Mark Zuckerberg and Sheryl Sandberg. I found the meeting deeply disturbing — but not for the reasons you might think."
  // "image" => "https://cdn-images-1.medium.com/max/1600/1*X7ZwkHWGyKdQk8yyg7feHg.png"
  // "video" => null

    $rand_keys = array_rand($content, 2);
    array_push($recommended,$content[$rand_keys[0]]);
    array_push($recommended,$content[$rand_keys[1]]);
  }

  //tutorial
  $username = Session::get('username');
  $user_info = DB::select('SELECT unseen_tutorial FROM users WHERE username=?',array($username));
  $show_tutorial = null;
  if (intval($user_info[0]->unseen_tutorial) == 1){
    $show_tutorial = true;
  }
  else {
    $show_tutorial = false;
  }

  //Article
  if ($type=='image'){
    if($api == 'pocket'){
      $article_id = Request::input('id');
      $article = $this->contentPocket($article_id);
      return View::make('articles.image-article',['content'=>$article, 'recommended' => $recommended,'show_tutorial'=>$show_tutorial]);
    }
  }
  if ($type=='video'){
    if ($api =='vimeo'){
      $id = Request::input('id');
      $tag = Request::input('tag');
      $article = $this->contentVimeo($id, $tag);
      return View::make('articles.video-article',['content'=>$article, 'recommended' => $recommended,'show_tutorial'=>$show_tutorial]);
    }
    if ($api == 'pocket'){
      $article_id = Request::input('id');
      $article = $this->contentPocket($article_id);
      return View::make('articles.video-article',['content'=>$article, 'recommended' => $recommended,'show_tutorial'=>$show_tutorial]);
    }
      if ($api == 'slideshare'){
        $article_id = Request::input('id');
        $article = $this->contentSlideshare($article_id);
        return View::make('articles.video-article',['content'=>$article, 'recommended' => $recommended,'show_tutorial'=>$show_tutorial]);
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
      return View::make('articles.code-article',['content'=>$article, 'recommended' => $recommended,'show_tutorial'=>$show_tutorial]);
    }
        // dd($article);
  }
  return view('layouts.article');
}

    //APIs

    //GITHUB
    //List github articles
public function listGithub(){
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

  $tags=DB::select("SELECT tagname FROM tags WHERE id_article = ? and source_name='pocket'", array($id));
  $file_content['tags'] = '';
  foreach($tags as $tag){
    $file_content['tags'].=$tag->tagname.', ';

  }
  $file_content['tags'] = rtrim($file_content['tags'],', ');

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
    $more_tags = 0;
    try{
      //verify if content is in database
      $result_video = DB::table('vimeo_articles')->select('url_content')->where('id_article','=',$id)->first();
      $vimeo_connection= Vimeo::connection('alternative');
      $vimeo_connection->setToken($access_token);
      $article = $vimeo_connection->request($result_video->url_content,[],'GET');
      $more_tags = 1;
    }catch(\Exception $e){
      //catch invalid_number exception from db => that means it's a recommended content
      $vimeo_connection =Vimeo::connection('main');
      $article = $vimeo_connection->request($id,[],'GET');
    }
    //construct file_content object to visualize
    $file_content['type'] = 'vimeo';
    $file_content['title'] = $article['body']['name'];
    $file_content['description'] = $article['body']['description'];

    if ($more_tags == 1){
      foreach($article['body']['tags'] as $current_tag){
        $tag = $tag.$current_tag['name']. ', ';
      }

      $tag = substr($tag, 0, strlen($tag)-2);
    }

    $file_content['details'] = $article['body']['user']['name'];
    $file_content['content'] = $article['body']['embed']['html'];
    $file_content['url'] = $article['body']['link'];
    $file_content['tags'] = $tag;
    return $file_content;
  }catch(\Exception $e){
    dd($e);
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
        } finally{
    return $content;
  }
}


public function contentSlideshare($id)
{
        $SS = new SlideshareController();
        $validation = $SS->generate_validation();
        $response = simplexml_load_string(file_get_contents('https://www.slideshare.net/api/2/get_slideshow/?'.$validation.'&slideshow_id='.$id));
        $file_content['type'] = 'slideshare';
        $file_content['title'] = $response->Title;
        $file_content['description'] = $response->Description;
        $file_content['details'] = $response->Username;
        $file_content['content'] = $response->Embed;
        $file_content['url'] = $response->URL;
        return $file_content;
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
