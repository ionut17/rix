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
use Session;

class RecommendedController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  private $max_files_per_api = 5;
  protected $rix_username;

  public function __construct(){
      // $this->middleware('auth');
    $this->rix_username = Session::get('username');
  }

  public function show($page_number=1){
    try{
      $contentGithub = null;
      $contentPocket = null;
      $contentSlideshare = $this->recommendedSlideshare();

      $contentVimeo = $this->recommendedVimeo();
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
      if ($contentSlideshare!=null) {
        if ($content != null){
          $content = array_merge($content, $contentSlideshare);
        }
        else {
          $content = array_merge($contentSlideshare);
        }
      }
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
      $has_accounts = true;
    }
    catch(\Exception $e){
      $has_accounts = false;
      $display_content = null;
      $page_count = 1;
      $page_numer = 1;
    }
    return View::make('content', ['has_accounts' => $has_accounts, 'content' => $display_content,'page_count'=>$page_count,'page_number'=>$page_number, 'target'=>'recommended']);
  }

  //API's recommendations

  public function recommendGithub($search_input,$languages){
    try{
      // $tags = DB::table('preferences')->select('tagname')->where('username',$this->rix_username)->get();
      $client = new \Github\Client();
      $result = DB::select('select access_token from accounts where username = ? and source_name = ?', array($this->rix_username,"github"));
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
            $file_content['id'] = '';
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

  public function recommendedVimeo(){
    //Get all the favourites tags for user
    $tags = DB::table('preferences')->select('tagname')->where('username',$this->rix_username)->get();
    $recommended_files = array();

    try{
      //For each tag
      foreach($tags as $tag){
        $url = '/tags/'.strtolower($tag->tagname).'/videos';
        $result = $vimeo_connection = Vimeo::connection()->request($url, [ 'page' => 1, 'per_page' => $this->max_files_per_api], 'GET');
        $articles = $result['body']['data'];
        foreach($articles as $article){
          $file_content['type'] = 'vimeo';
          $file_content['title'] = $article['name'];
          $file_content['description'] = $article['description'];
          $file_content['details'] = 'Author:  '. $article['user']['name'];
          $file_content['content'] = $article['embed']['html'];
          $file_content['url'] = $article['uri'];
          $file_content['tag'] = $tag->tagname;
          array_push($recommended_files, $file_content);
        } 
      }
    }catch(\Exception $e){
      dd($e);
      $recommended_files = null;
    }finally{
      return $recommended_files;
    }
  }

public function recommendedSlideshare()
{

    $tags = DB::table('preferences')->select('tagname')->where('username',$this->rix_username)->get();
    $recommended_files = array();
    try 
    {

        foreach($tags as $tag)
        {
            $SS = new SlideshareController();
            $validation = $SS->generate_validation();

            $results = simplexml_load_string(file_get_contents('https://www.slideshare.net/api/2/get_slideshows_by_tag/?'.$validation.'&tag='.$tag->tagname.'&limit='.$this->max_files_per_api));
            $results = $results->Slideshow;
        
            foreach($results as $result)
            {
                $file_content = array();
                $file_content['type'] = 'slideshare';
                $file_content['id'] = $result[0]->id_article;
                $file_content['title'] = $result[0] ->title;
                $file_content['details'] = $result[0] ->author;
                $file_content['description'] = $result[0]->description;
                $file_content['image'] = $result[0]->image_url;
                dd($file_content);
                array_push($recommended_files,$file_content);
            }
        }
    }
    catch(\Exception $e)
    {
        dd($e);1
        $recommended_files = null;
    }
    finally
    {
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
