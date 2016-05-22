<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use venor\SSUtil\SSUtil;
use Session;
use Request;
use DB;
use DOMDocument;
use Redirect;

class SlideshareController extends BaseController
{
	private $api_key;
	private $shared_secret;

	public function __construct()
	{
		$this->api_key = 'jQDB5EEq';
		$this->shared_secret = 'jnrS8csn';
	}

	private function generate_validation()
	{
		$time = time();
		$validation = 'api_key='.$this->api_key.'&ts='.$time.'&hash='.sha1($this->shared_secret.$time);
		return $validation;
	}
	public function authorize()
	{

		$slideshare_username = Request::get('slideshare_username');
		$username = Session::get('username');

		//Get the XML with presentations
		$response = simplexml_load_string(file_get_contents('https://www.slideshare.net/api/2/get_slideshows_by_user/?'.$this->generate_validation().'&username_for='.$slideshare_username.'&limit=50'));
		if(array_key_exists('Message', $response))
		{
			return Redirect::to('settings')->with('slideshare_error','Invalid slideshare username.');
		}
		else
		{
			$min = min($response->Count,20);
			for($i = 0; $i < $min; $i++)
			{
				dd($response->Slideshow[$i]);
			}

			DB::statement('insert into accounts(username,access_token,source_name) values (?,?,?)',array($username,$slideshare_username,'slideshare'));
			Redirect::to('settings');
		}
	}
}
