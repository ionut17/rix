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

	public function generate_validation()
	{
		$time = time();
		$validation = 'api_key='.$this->api_key.'&ts='.$time.'&hash='.sha1($this->shared_secret.$time);
		return $validation;
	}
	public function store()
	{

	}
	public function authorize()
	{
		$username = Session::get('username');
		$result = DB::table('accounts')->where('username','=',$username)->where('source_name','=','slideshare')->select('access_token')->get();
		if(empty($result))
		{
			$slideshare_username = Request::get('slideshare_username');
		}
		else
		{
			DB::table('accounts')->where('username','=',$username)->where('source_name','=','slideshare')->delete();
			$slideshare_username = $result[0]->access_token;
		}
		//Get the XML with presentations
		$response = simplexml_load_string(file_get_contents('https://www.slideshare.net/api/2/get_slideshows_by_user/?'.$this->generate_validation().'&username_for='.$slideshare_username.'&limit=50'));
		if(array_key_exists('Message', $response))
		{
			return Redirect::to('settings')->with('slideshare_error','Invalid slideshare username.');
		}
		else
		{	

			//create the account
			DB::statement('insert into accounts(username,access_token,source_name) values (?,?,?)',array($username,$slideshare_username,'slideshare'));
			//store the data for showing
			$min = min($response->Count,50);
			for($i = 0; $i < $min; $i++)
			{	
				$DB_id_article = (string)$response->Slideshow[$i]->ID;
				$DB_title = (string)$response->Slideshow[$i]->Title[0];
				$DB_description = (string)$response->Slideshow[$i]->Description;
				$DB_image_url = (string)$response->Slideshow[$i]->ThumbnailXLargeURL;

				DB::insert('insert into slideshare_articles(id_article,username,author,title,description,image_url) values (?,?,?,?,?,?)',array($DB_id_article,$username,$slideshare_username,$DB_title,$DB_description,$DB_image_url));
			}	

			return Redirect::to('settings');
		}
	}
}
