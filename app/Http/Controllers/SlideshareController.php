<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use venor\SSUtil\SSUtil;

use Request;
use DB;

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
		$response=file_get_contents('https://www.slideshare.net/api/2/get_slideshow/?'.$this->generate_validation().'&slideshow_id=62158286');

		 dd($response);


		// header('Location: http://localhost:2000/activate/slideshare');
		// exit();
	}

	public function activate()
	{
		echo $this->api_key.' activate '.$this->shared_secret;
		header('Location: http://localhost:2000/mycontent');
		exit();
	}

}
