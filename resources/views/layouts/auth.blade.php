@extends('layouts.master')

@section('title')
  @yield('auth-title')
@endsection

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/main.css') }}" type="text/css">
@endsection

@section('content')

  <div class="vertical-align">
    <div class="row">
      <div class="col-lg-4 col-md-6 col-sm-8 col-xs-12 col-md-offset-2 col-sm-offset-1 col-xs-offset-0">
        @yield('auth-content')
      </div>
    </div>
  </div>

@endsection

@section('scripts')
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.js"></script>
  <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.js"><\/script>')</script>

  <script src="js/main.js"></script>
@endsection
