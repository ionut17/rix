<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;

// use Illuminate\Http\Request;

// use App\Http\Requests;
use Vinkla\Vimeo\Facades\Vimeo;
use DB;
use Request;
use Session;

const REDIRECT_URI = 'http://localhost:2000/activate/vimeo';

class VimeoController extends BaseController
{
	// private $current_token;

	public function authorize(){
		Session::put('state', base64_encode(openssl_random_pseudo_bytes(30)));
		$conn = Vimeo::connection('alternative');
		header("Location: ".$conn -> buildAuthorizationEndpoint(REDIRECT_URI, 'private', session('state')));
		exit();
	}

	public function activate(){

		if (session('state') != Request::input('state')) {
			echo 'Something is wrong. Vimeo sent back a different state than this script was expecting. Please let vimeo know that this has happened';
		}
		$code = Request::input('code');
		$tokens = Vimeo::connection('alternative')->accessToken($code, REDIRECT_URI);
		if ($tokens['status'] == 200) {
			Session::put('access_token', $tokens['body']['access_token']);
		} else {
			echo "Unsuccessful authentication";
		}
		$vimeo_connection = Vimeo::connection('alternative');
		$vimeo_connection->setToken($tokens['body']['access_token']);
		$username = 'admin';

		//added the access token for the current user in the DB
		DB::table('accounts') -> insert(['username' => $username, 'access_token' => $tokens['body']['access_token'], 'source_name' => 'vimeo']);

		$pdo = DB::getPdo();
		// request to get all the videos from the user vimeo account
		$response = $vimeo_connection -> request('/me/videos', [], 'GET');
		$videos = $response['body']['data'];

		$stmt = $pdo -> prepare("BEGIN
			articles_package.add_varticle(:username, :title, :description, :url_content, :authors, :is_public);
			END;");

		foreach($videos as $video){
			$title = $video['name'];
			$description = $video['description'];
			$url_content = $video['link'];
			$authors = $video['user']['name'];
			if ($video['status'] == 'available')
				$is_public = 't';
			else $is_public = 'f';

			$stmt ->bindParam(':username', $username);
			$stmt ->bindParam(':title', $title);
			$stmt ->bindParam(':description', $description);
			$stmt ->bindParam(':url_content', $url_content);
			$stmt ->bindParam(':authors', $authors);
			$stmt ->bindParam(':is_public', $is_public);
			$stmt ->execute();
		}

		header('Location: http://localhost:2000/mycontent');
		exit();
	}

}
