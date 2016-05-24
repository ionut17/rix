@extends('layouts.master')

@section('title')
  @yield('article-title')
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
          <li><a href="{{ URL::to('/mycontent') }}">My Content</a></li>
          <li><a href="{{ URL::to('/recommended') }}">Recommended Content</a></li>
          <li><a href="">More</a></li>
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

  <div class="container article-wrapper">
    <div class="article-container">
      @yield('article-content')
    </div>
    <div class="content-container side">
      <div class="side-title">
        Recommended content
      </div>
      <div class="side-content">
        @for ($i = 0; $i < 2; $i++)
            <div class="article-box">
              <section class="image" style="background-image: url('{{ asset('img/articles/'.($i%4+1).'.jpg') }}')"></section>
              <section class="tag">
                @if ($i%4==0)
                  Github
                @elseif ($i%4==1)
                  Feedly
                @elseif ($i%4==2)
                  Slideshare
                @else
                  Vimeo
                @endif
              </section>
              <section class="content">
                <h2>Article {{$i+1}}</h2>
                <label>posted 8 March 2016, 20 comments</label>
                <p class="description">
                  Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                </p>
              </section>
              <a href="#">
                <button type="button" name="view-btn" class="article-button">Read</button>
              </a>
            </div>
        @endfor
      </div>
      <div class="footer-container">
        <button type="button">Load More</button>
      </div>
    </div>
  </div>

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

  <script src="{{ asset('js/main.js') }}"></script>
@endsection
