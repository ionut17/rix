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

    private $max_files_per_api = 1;
    protected $rix_username;

    public function __construct(){
      // $this->middleware('auth');
        $this->rix_username = Session::get('username');
    }

    public function buildRecommendedContent(){
    //Verify if user has any account
        $rix_username = Session::get('username');
        $accounts = DB::table('accounts')->where('username', '=', $rix_username)->select('source_name')->count();
        $has_accounts = $accounts;

        $content = array();

        $vimeo_recommended = null;
        $slideshare_recommended = null;
        $github_recommended = null;

        $result = DB::table('vimeo_recommended')->where('username', $rix_username)->count();
        if ($result == 0)
          $this->storeRecommendVimeo();
      $vimeo_recommended = $this ->recommendVimeo();
      if($content == null)
          $content = array_merge($vimeo_recommended);
      else $content = array_merge($content, $vimeo_recommended);

        //Github
      $result = DB::table('github_recommended')->where('username', $rix_username)->count();
      if ($result == 0){
      $this->storeRecommendGithub();
      }
      $github_recommended = $this ->recommendGithub();
      if($content == null)
          $content = array_merge($github_recommended);
      else $content = array_merge($content, $github_recommended);
        //Slideshare
      $result = DB::table('slideshare_recommended')->where('username',$rix_username)->count();
      if($result == 0)
        $this->storeRecommendSlideshare();
    $slideshare_recommended = $this ->recommendSlideshare();
    if($content == null)
        $content = array_merge($slideshare_recommended);
    else
        $content = array_merge($content,$slideshare_recommended);


  //If there is any content collected we shuffle it
    if ($content != null)
      shuffle($content);
  Session::put('content', $content);
  Session::put('has_accounts', $has_accounts);
  Session::save();
  return $this->show();
  }

public function show($page_number=1){
    $content = Session::get('content');

    $page_number = intval($page_number);
    $per_page = 8;

    $article_count = count($content);
    $page_count = intval(ceil($article_count/$per_page));
    $index_start = ($page_number-1) * $per_page;
    $display_content = null;

    if ($content != null)
      $display_content = array_slice($content, $index_start, $per_page);
  $select_values = array('github', 'vimeo', 'slideshare');

  return View::make('content', ['has_accounts' => Session::get('has_accounts'),'content' => $display_content,'page_count'=>$page_count,'page_number'=>$page_number,'select_values'=>$select_values]);
}

  //API's recommendations

public function recommendVimeo(){
    //Get from DB all the recommendations collected for the current user
    $content = array();
    try{
      $username = Session::get('username');
      $results = DB::select('select id_article from vimeo_recommended where username = ? ', array($username));
      foreach($results as $result){
        $video = DB::select('SELECT id_article, title, description, authors, tagname FROM vimeo_recommended WHERE id_article = ?', array($result->id_article));
        $file_content['type'] = 'vimeo';
        $file_content['title'] = $video[0] ->title;
        $file_content['details'] = $video[0] ->authors;
        $file_content['description'] = $video[0]->description;
        $file_content['id'] = $video[0]->id_article;
        $file_content['tag'] = $video[0]->tagname;
        array_push($content, $file_content);
    }
}catch(\Exception $e){
  //dd($e);
  $content = null;
}finally{
  return $content;
}
}

public function storeRecommendVimeo(){
    //Get all the favourites tags for user
    $tags = DB::table('preferences')->select('tagname')->where('username',$this->rix_username)->get();
    $recommended_files = array();

    $pdo = DB::getPdo();
    $stmt = $pdo -> prepare("BEGIN
      :id := articles_package.add_rvarticle(:username, :title, :description, :url_content, :authors, :is_public, :tagname);
      END;");
    $id = 100000;

    //For each tag
    foreach($tags as $tag){
      $url = '/tags/'.strtolower($tag->tagname).'/videos';
      $result = $vimeo_connection = Vimeo::connection()->request($url, [ 'page' => 1, 'per_page' => $this->max_files_per_api], 'GET');
      $articles = $result['body']['data'];

      //Collect articles for each tag and insert into the DB
      foreach($articles as $video){
        $username = Session::get('username');
        $title = $video['name'];
        $description = $video['description'];
        $url_content = $video['uri'];
        $authors = $video['user']['name'];
        $tagnm = $tag->tagname;
        if ($video['status'] == 'available')
          $is_public = 't';
      else $is_public = 'f';
      $stmt->bindParam(':id', $id);
      $stmt ->bindParam(':username', $username);
      $stmt ->bindParam(':title', $title);
      $stmt ->bindParam(':description', $description);
      $stmt ->bindParam(':url_content', $url_content);
      $stmt ->bindParam(':authors', $authors);
      $stmt ->bindParam(':is_public', $is_public);
      $stmt->bindParam(':tagname',$tagnm);
      $stmt ->execute();
      }
}
}


public function storeRecommendGithub(){
    $tags = DB::table('preferences')->select('tagname')->where('username',$this->rix_username)->get();
    try{
    //Authenticate the user to get recommandations
        $rix_username = Session::get("username");
        $client = new \Github\Client();
        $result = DB::select('select access_token from accounts where username = ? and source_name = ?', array($rix_username,"github"));
        if(!empty($result)){
          $token = $result[0]->access_token;
          $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);

      //Get files for every tag in the DB for the current user
          foreach($tags as $tag){

              $search_input = $tag->tagname;
              $repos = $client->api('repo')->find($search_input);;
              $count = 0;
              $counted_files = 0;
              while($counted_files<$this->max_files_per_api){
                $count = $count + 1;
          // dd($repos['repositories'][$count]['owner']);
                $files = $client ->api('repo')->contents()->show($repos['repositories'][$count]['owner'],$repos['repositories'][$count]['name'],'.');

                foreach($files as $file){
                  if($counted_files < $this->max_files_per_api){
                    $counted_files = $counted_files + 1;
                    if($file['type']=='file' && $file['size']<1000000){
                      $repo_name = $repos['repositories'][$count]['name'];
                      $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                      $id = null;
                      $result = DB::statement('BEGIN articles_package.add_rgarticle(?,?,?,?,?,?,?,?,?); END;', array($rix_username, $repo_name, $file['path'], $file['name'], $extension, '', 'f', $tag->tagname, $repos['repositories'][$count]['owner']));
                  }
              }
              else break;
          }
      }
  }

}
}catch(\Exception $e){
    // dd($e);
}
}

public function recommendGithub(){
    $client = new \Github\Client();
    try {
      $rix_username = Session::get("username");
      $results = DB::select('SELECT id_article FROM github_recommended WHERE username=?',array($rix_username));
      // dd($results);
      $content = array();
      foreach ($results as $result){
        //Selecting values from db
        $values=DB::select('SELECT id_article, title, repo, path, extension, owner_name FROM github_recommended WHERE id_article = ?', array($result->id_article));

        //Saving values to object
        $file_content['type']='github';
        $file_content['id'] = $values[0]->id_article;
        $file_content['title'] = $values[0]->title;
        $file_content['details'] = $values[0]->repo.'\\'.$values[0]->path;
        $file_content['tag'] = $values[0]->extension;
        $file_content['username'] = $values[0]->owner_name;
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

public function recommendSlideshare()
{
    $content = array();
    try
    {
        $username = Session::get('username');
        $results = DB::select('select id_article from slideshare_recommended where username = ?',array($username));
        foreach($results as $result)
        {
            $video = DB::select('SELECT id_article, title, description, author,image_url FROM slideshare_recommended WHERE id_article = ?', array($result->id_article));
            $file_content['type'] = 'slideshare';
            $file_content['id'] = $video[0]->id_article;
            $file_content['title'] = $video[0] ->title;
            $file_content['details'] = $video[0]->author;
            $file_content['description'] = $video[0]->description;
            $file_content['image'] = $video[0]->image_url;
            $file_content['tag'] = '';
            array_push($content, $file_content);
        }
    }
    catch(\Exception $e)
    {
        //dd($e);
        $content = null;
    }
    finally
    {
        return $content;
    }
}

public function storeRecommendSlideshare() {
    try
    {
        $tags = DB::table('preferences')->select('tagname')->where('username',$this->rix_username)->get();
        $recommended_files = array();

        $pdo = DB::getPdo();
        $stmt = $pdo -> prepare("BEGIN
          articles_package.add_rsarticle(:new_id, :v_username, :v_author, :v_title, :v_description, :v_image_url);
          END;");

        foreach($tags as $tag)
        {
            $SS = new SlideshareController();
            $validation = $SS->generate_validation();
            $results = simplexml_load_string(file_get_contents('https://www.slideshare.net/api/2/get_slideshows_by_tag/?'.$validation.'&tag='.$tag->tagname.'&limit='.$this->max_files_per_api));
            $results = $results->Slideshow;
            foreach($results as $result)
            {
                $id = json_decode($result->ID);
                $username = Session::get('username');
                $details = (string)$result->Username;
                $title = (string)$result->Title;
                $description = (string)$result[0]->Description;
                $image = (string)$result[0]->ThumbnailXLargeURL;

                $stmt->bindParam(':new_id',$id);
                $stmt->bindParam(':v_username',$username);
                $stmt->bindParam(':v_author',$details);
                $stmt->bindParam(':v_title',$title);
                $stmt->bindParam(':v_description',$description);
                $stmt->bindParam(':v_image_url',$image);
                $stmt->execute();
            }
        }
    }
    catch (\Exception $e){
      // dd($e->getMessage());
      $recommended_files = null;
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
