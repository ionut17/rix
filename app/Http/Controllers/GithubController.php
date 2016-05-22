<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Github\Client as GithubClient;
use Request;
use Session;
use DB;

class GithubController extends BaseController
{

  private $client;
  private $client_secret;

  public function __construct(GithubClient $client)
  {
    $this->client = env('GITHUB_CLIENT');
    $this->client_secret = env('GITHUB_CLIENT_SECRET');
  }

  public function authorize(){
    //Setting API's scope
    $scope = 'repo';
    //Requesting authorization
    header('Location: https://github.com/login/oauth/authorize?client_id='.$this->client.'&redirect_uri=http://localhost:2000/activate/github'.'&scope='.$scope);
    exit();
  }

  public function activate(){
    try{
      $code = Request::input('code');
      //Making post requests
      $url = 'https://github.com/login/oauth/access_token';
      $data = array('client_id' => $this->client, 'client_secret' => $this->client_secret, 'code' => $code);
      $options = array(
          'http' => array(
              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
              'method'  => 'POST',
              'content' => http_build_query($data)
          )
      );
      $context  = stream_context_create($options);
      //Sending post requests
      $result = file_get_contents($url, false, $context);
      //Get access token
      $pieces = explode('&',$result);
      $pieces2 = explode('=',$pieces[0]);
      $access_token = $pieces2[1];
      //HERE STORE IT IN DATABSE FOR THE CURRENT USER
      $rix_username = Session::get("username");
      DB::statement('insert into accounts (username,access_token,source_name) values (?,?,?)', array($rix_username, $access_token, 'github'));
      // DB::statement('insert into accounts (id_account, username, access_token, source_name) values (?, ?, ?, ?)', array(1, $username, $access_token, 'github'));
      $this->store();
    }
    catch (/Exception $e){
      return redirect('mycontent');
    }
    //Redirect to content
    return redirect('mycontent');
  }

  public function store(){
    //Connection
    $client = new \Github\Client();
    try {
      $rix_username = Session::get("username");
      $result = DB::select('select access_token from accounts where username = ? and source_name = ?', array($rix_username,"github"));
      $token = $result[0]->access_token;
      $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
      $repos = $client->api('current_user')->repositories();
      $account = $repos[0]['owner']['login'];
      $username = $rix_username;
      //Content
      $path = '.';
      // $content = null;
      // $content = array();
      foreach ($repos as $repo){
          $files = $client->api('repo')->contents()->show($account, $repo['name'], '.'); // dd($repo);
          // $tags = $client->api('repo')->tags($account, $repo['name']);
        foreach( $files as $file){
          if ($file['type']=='file' && $file['size']<1000000){
              $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
              $id = null;
              $result = DB::statement('BEGIN articles_package.add_garticle(?,?,?,?,?,?,?); END;', array($rix_username, $repo['name'], $file['path'], $file['name'], $extension, '', 'f'));
              // $myfile = $client->api('repo')->contents()->show('ionut17', $repo['name'], $file['path']);
            //   $file_content['type'] = 'github';
            //   $file_content['title'] = $file['name'];
            //   $file_content['repo'] = $repo['name'];
            //   $file_content['path'] = $file['path'];
            // $file_content['description'] = $file['description'];
            //   $file_content['username'] = '';
            //   $file_content['tag'] = $extension;
              // $file_content['content'] = base64_decode($myfile['content']);
              // dd($myfile);
              // array_push($content,$file_content);
          }
        }
      }
    } catch (\Exception $e) {
        // dd($e->getMessage());
    }
  }

}
