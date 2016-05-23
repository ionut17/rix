<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Github\Client as GithubClient;
use Duellsy\Pockpack\Pockpack;
use Duellsy\Pockpack\PockpackAuth;
use Duellsy\Pockpack\PockpackQueue;
use Request;
use Session;
use DB;

class PocketController extends BaseController
{

  private $consumer_key;

  public function __construct(GithubClient $client)
  {
    $this->consumer_key = env('POCKET_CONSUMER_KEY');
  }

  //Pocket
  public function authorize(){
    //Getting request token
    $pockpath_auth = new PockpackAuth();
    $request_token = $pockpath_auth->connect($this->consumer_key);
    //Redirect to authorization
    header("Location: https://getpocket.com/auth/authorize?request_token=".$request_token."&redirect_uri=http://localhost:2000/activate/pocket?request_token=".$request_token);
    exit();
  }

  //IMPORTANT: for DB: access token
  public function activate(){
    $pockpath_auth = new PockpackAuth();
    $local_request_token=Request::input('request_token');
    //Getting access token
    $access_array = $pockpath_auth->receiveTokenAndUsername($this->consumer_key, $local_request_token);
    $access_token = $access_array['access_token'];
    //Storing the pocket access token into the DB
    $username = Session::get('username');
    DB::statement('insert into accounts values (?,?,?)', array($username, $access_token, 'pocket'));
    //Storing pocket content into DB
    $this->store();
    //Redirect to content
    return redirect('mycontent');
    exit();
  }

  public function store(){
    $consumer_key=env('POCKET_CONSUMER_KEY');
    // try{
      $rix_username = Session::get('username');
      $result= DB::select('select access_token from accounts where username = ? and source_name = ?', array($rix_username,"pocket"));
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
      foreach($articles_list as $value){
        $file_content=array();
          if(isset($value['authors'])){
            foreach ($value['authors'] as $author_prop) {
              $authors_string=null;
              $authors_string.=$author_prop['name'].' - '.$author_prop['url'].' | ';
            }
            $file_content['authors']=$authors_string;
        }
        else{
          $file_content['authors']=null;
        }
          if($value['has_image']==1){
            $file_content['image']=$value['images'][1]['src'];
        }
        else{
          $file_content['image']=null;
        }
          if(isset($value['tags'])){
            $file_content['tags']=$value['tags'];
        }
        else{
          $file_content['tags']=null;
        }
          if($value['has_video']!=0){
            $file_content['video']=$value['videos'][1]['src'];
          }
          else{
            $file_content['video']=null;
          }

          $result = DB::statement('BEGIN articles_package.add_particle(?,?,?,?,?,?,?); END;', array($rix_username, $value['resolved_title'],$value['resolved_url'],$value['excerpt'],$file_content['image'],$file_content['video'],$file_content['authors']));
          // DB::statement('insert into tags values (?,?,?)', array($username, $access_token, 'pocket'));

      }
    // } catch (\Exception $e) {
    //   dd($e->getMessage());
    // }
  }
}
