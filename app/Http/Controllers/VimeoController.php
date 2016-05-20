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

		$access_token = Request::input('access_token');
		Session::put('state', base64_encode(openssl_random_pseudo_bytes(30)));
		echo session('state');
		if (empty($access_token)){        
			$conn = Vimeo::connection('alternative');
			header("Location: ".$conn -> buildAuthorizationEndpoint(REDIRECT_URI, 'private', session('state')));
		}
		else echo "WOAH!!"; 
		exit();
	}

	public function activate(){

		// if (session('state') != Request::input('state')) {
		// 	echo 'Something is wrong. Vimeo sent back a different state than this script was expecting. Please let vimeo know that this has happened';
		// }
		$code = Request::input('code');
		$tokens = Vimeo::connection('alternative')->accessToken($code, REDIRECT_URI);
		if ($tokens['status'] == 200) {
			Session::put('access_token', $tokens['body']['access_token']);
			echo "Succesful authentication.";
		} else {
			echo "Unsuccessful authentication";
		}
		Vimeo::connection('alternative') -> setToken($tokens['body']['access_token']);
		$username = 'admin';
		DB::statement('insert into accounts (id_account, username, access_token, source_name) values (?, ?, ?, ?)', array(1, $username, $tokens['body']['access_token'], 'vimeo'));

		header('Location: http://localhost:2000/mycontent');
		exit();
	}

}
