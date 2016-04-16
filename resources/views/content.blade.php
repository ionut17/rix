@extends('layouts.master')

@section('title')
  My Content
@endsection

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/main.css') }}" type="text/css">
@endsection

@section('navigation')
  <nav class="navigation-wrapper">
    <div class="container">
      <div class="navigation-container">
        <a href="{{ URL::to('/mycontent') }}" style="text-decoration:none;">
          <figure class="logo small">
            <img src="{{asset('img/rix_logo.svg')}}" draggable="false"/>
            <label for="">Resource Interactive Explorer</label>
          </figure>
        </a>
        <ul class="nav-items">
          <li>
            <a href="#">
            <i class="fa fa-cog" aria-hidden="true"></i>
            Settings
            </a>
          </li>
          <li>
            <a href="#">
            <i class="fa fa-user" aria-hidden="true"></i>
            My Account
            </a>
          </li>
          <li>
            <a href="{{ URL::to('/login') }}">
            <i class="fa fa-sign-out" aria-hidden="true"></i>
            Log out
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <section class="status-wrapper">
    <div class="container">
      <div class="status-container">
        <ul class="status-list">
          <li>Recommended Content</li>
          <li>My Content</li>
          <li>More</li>
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
    <div class="content-container">
      @for ($i = 0; $i < 16; $i++)
          <div class="article-box">
            <section class="image" style="background-image: url('{{ asset('img/test.jpg') }}')"></section>
            <section class="content">
              <h2>Article {{$i+1}}</h2>
              <p class="description">
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
              </p>
            </section>
            <a href="#">
              <button type="button" name="view-btn" class="button">Read</button>
            </a>
          </div>
      @endfor
    </div>
  </div>

@endsection

@section('scripts')
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.js"></script>
  <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.js"><\/script>')</script>

  <script src="js/main.js"></script>
@endsection
