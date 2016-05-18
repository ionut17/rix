<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Github\Client as GithubClient;
use Request;

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
    header("Location: https://getpocket.com/auth/authorize?request_token=".$request_token."&redirect_uri=http://localhost:3000/activate/pocket?request_token=".$request_token);
    exit();
  }

  //IMPORTANT: for DB: access token
  public function activate(){
    $pockpath_auth = new PockpackAuth();
    $local_request_token=Request::input('request_token');
    //Getting access token
    $access_array = $pockpath_auth->receiveTokenAndUsername($consumer_key, $local_request_token);
    $access_token = $access_array['access_token'];
    //HERE STORE THE ACCESS TOKEN
    header('Location: http://localhost:2000/mycontent?pocket_access='.$access_token);
    exit();
  }

}
