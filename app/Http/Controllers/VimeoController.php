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

	public function authorize(){
		Session::put('state_vimeo', base64_encode(openssl_random_pseudo_bytes(30)));
		Session::save();
		$conn = Vimeo::connection('alternative');
		header("Location: ".$conn -> buildAuthorizationEndpoint(REDIRECT_URI, 'private', Session::get('state_vimeo')));
		exit();
	}

	public function activate(){
		if (Session::get('state_vimeo') != Request::input('state')) {
			echo 'Something is wrong. Vimeo sent back a different state than this script was expecting. Please let vimeo know that this has happened';
		}
		try{
			$code = Request::input('code');
			$tokens = Vimeo::connection('alternative')->accessToken($code, REDIRECT_URI);
			if ($tokens['status'] == 200) {
				Session::put('access_token', $tokens['body']['access_token']);
			} else {
				echo "Unsuccessful authentication";
			}
			$vimeo_connection = Vimeo::connection('alternative');
			$vimeo_connection->setToken($tokens['body']['access_token']);
			$username = Session::get('username');

		//added the access token for the current user in the DB
			DB::table('accounts') -> insert(['username' => $username, 'access_token' => $tokens['body']['access_token'], 'source_name' => 'vimeo']);

			$pdo = DB::getPdo();
		// request to get all the videos from the user vimeo account
			$response = $vimeo_connection -> request('/me/videos', [], 'GET');
			$videos = $response['body']['data'];
			$stmt = $pdo -> prepare("BEGIN
				:id := articles_package.add_varticle(:username, :title, :description, :url_content, :authors, :is_public);
				END;");
			$id = 100000;

			foreach($videos as $video){
				$title = $video['name'];
				$description = $video['description'];
				$url_content = $video['uri'];
				$authors = $video['user']['name'];
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
				$stmt ->execute();

				foreach($video['tags'] as $tag){
					DB::table('tags') ->insert(['id_article' => $id, 'source_name' => 'vimeo', 'tagname' => $tag['name']]);
					$statement = $pdo->prepare("BEGIN
						articles_package.insert_user_tags(:username,:tagname);
						END;");
					$statement ->bindParam(':username', $username);
					$statement ->bindParam(':tagname', $tag['tag']);
					$statement->execute();
				}
			}

			header('Location: http://localhost:2000/mycontent');
			exit();
		}catch(\Exception $e){
			dd($e);
			header('Location: http://localhost:2000/mycontent');
			exit();
		}
	}

}
