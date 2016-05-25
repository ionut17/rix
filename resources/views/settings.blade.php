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
        <section class="search" id="search-box">
          <input type="text" name="search" id="search" class="search-box" placeholder="Search">
          <ul class="search-results" id="search-results"></ul>
          <i class="fa fa-times-circle" aria-hidden="true" id="search-clear"></i>
          <i class="fa fa-search" aria-hidden="true" id="search-normal"></i>
        </section>
      </div>
    </div>
  </section>
@endsection

@section('content')

  <div class="container">
    <div class="row settings-wrapper">
      <div class="settings-box accounts">
        <!-- Added dinamicaly -->
        <div class="accounts-box">
          <h2 class="title">Attached accounts</h2>
          @if (isset($sources))
            @foreach($sources as $source)
              <section class="account-row">
                <label>{{$source}}</label>
                <div>
                  <span class="status green">CONNECTED</span>
                  <i class="fa fa-times-circle-o" aria-hidden="true" data-toggle="modal" data-target="#{{$source}}RemoveConfirm"></i>
                </div>
              </section>
              @include ('modals.remove-account')
            @endforeach
          @endif
        </div>
        <div class="accounts-buttons">
          <h2 class="title">Actions</h2>
          <button type="button" name="view-btn" class="article-button" data-toggle="modal" data-target="#addModal">Connect account</button>
          <a href="{{ URL::to('/refresh') }}">
            <button type="button" name="view-btn" class="article-button" data-toggle="modal" data-target="#refreshModal" id="refresh-content">Refresh content</button>
          </a>
          <a href="{{ URL::to('/generatetoken') }}">
            <button type="button" name="view-btn" class="article-button">Generate API token</button>
          </a>
          <a href="{{ URL::to('/showtutorial') }}">
            <button type="button" name="view-btn" class="article-button">Show tutorial</button>
          </a>
        </div>
      </div>
      <div class="settings-box">
        <a href=""></a>
        <section class="profile-image"
          @if (isset($user_info->avatar))
            style="background-image: url('{{asset('img/profiles/'.$user_info->avatar.'.jpg')}}');"
          @endif
          >
          <figure class="hover-figure" data-toggle="modal" data-target="#editModal"><i class="fa fa-pencil" aria-hidden="true"></i></figure>
        </section>
        <div class="box">
          <label for="username">@if (isset($user_info)) {{$user_info->username}} @endif</label>
          <input type="text" name="username" id="username" class="hide">
          <label for="email">@if (isset($user_info)) {{$user_info->email}} @endif</label>
          <input type="text" name="email" id="email" class="hide">
        </div>
      </div>
    </div>
    @if (isset($slideshare_error))
      <div class="notification">
          <p class="error">Slideshare: {{$slideshare_error}}</p>
      </div>
    @endif
  </div>

  @include ('modals.attach-account')
  @include ('modals.edit-account')

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
  <!-- <script src="{{ asset('js/tutorial.js') }}"></script> -->
@endsection
