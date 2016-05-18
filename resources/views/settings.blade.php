@extends('layouts.master')

@section('title')
  Settings
@endsection

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/main.css') }}" type="text/css">
@endsection

@section('navigation')
  @include ('includes.menu')
  <section class="status-wrapper">
    <div class="container">
      <div class="status-container">
        <ul class="status-list">
          <li><a href="{{ URL::to('/settings') }}">Settings</a></li>
        </ul>
        <section class="search">
          <input type="text" name="search" id="search" class="search-box" placeholder="Search">
          <i class="fa fa-search" aria-hidden="true"></i>
        </section>
      </div>
    </div>
  </section>
@endsection

@section('content')

  <div class="container">
    <div class="row settings-wrapper">
      <div class="settings-box">
        <a href=""></a>
        <section class="profile-image" style="background-image: url('{{asset('img/profiles/avatar.jpg')}}');">
          <figure class="hover-figure"><i class="fa fa-pencil" aria-hidden="true"></i></figure>
        </section>
        <div class="box">
          <label for="username">Iacob Ionut</label>
          <input type="text" name="username" id="username" class="hide">
          <label for="email">ionut.iacob17@gmail.com</label>
          <input type="text" name="email" id="email" class="hide">
        </div>
      </div>
      <div class="settings-box accounts">
        <section class="account-row">
          <label>Github</label>
          <span class="status green">CONNECTED</span>
        </section>
        <section class="account-row">
          <label>Vimeo</label>
          <span class="status red">ERROR</span>
        </section>
        <button type="button" name="view-btn" class="article-button" data-toggle="modal" data-target="#addModal">Add account</button>
      </div>
    </div>
  </div>

  @include ('modals.attach-account')

@endsection

@section('footer')
  <div class="container-fluid">
    <div class="footer-container">
      <p>© Copyright RiX 2016 - <a href="{{asset('RIX-doc/index.html')}}">Documentation</a></p>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.js"></script>
  <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.js"><\/script>')</script>

  <script src="{{ asset('js/bootstrap/bootstrap.min.js')}}"></script>
  <script src="{{ asset('js/main.js') }}"></script>
@endsection