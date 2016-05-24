<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Request;
use Session;
use Redirect;

class FilterController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function save(){
      // return (Request::input('github'),Request::input('pocket'),Request::input('slideshare'),Request::input('vimeo'));
      $filter=array();
      $filter['github'] = Request::input('github');
      $filter['pocket'] = Request::input('pocket');
      $filter['slideshare'] = Request::input('slideshare');
      $filter['vimeo'] = Request::input('vimeo');
      Session::put('filter',$filter);
      Session::save();
      Redirect::to('mycontent');
      return Session::get('filter');
    }

    public function get(){
      return Session::get('filter');
    }
}
