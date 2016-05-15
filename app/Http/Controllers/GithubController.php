<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Github\Client as GithubClient;

class GithubController extends InterfaceController
{

  private $client;
  private $username;

  public function __construct(GithubClient $client)
  {
    $this->client = $client;
    $this->username = env('GITHUB_USERNAME');
  }

  public function getData(){
    try {
      $repos = $this->client->api('current_user')->repositories();
	    return $repos;
    } catch (\RuntimeException $e) {
      $this->handleAPIException($e);
    }
  }

}
