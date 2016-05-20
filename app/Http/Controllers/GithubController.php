<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Github\Client as GithubClient;
use Request;
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
    $username = 'admin';
    DB::statement('insert into accounts (id_account, username, access_token, source_name) values (?, ?, ?, ?)', array(1, $username, $access_token, 'github'));
    //Redirect to content
    header('Location: http://localhost:2000/mycontent');
    exit();
  }

}
