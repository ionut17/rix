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
          <li id="recommended-generate"><a href="{{ URL::to('/recommended') }}">Recommended Content</a></li>
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
        More content
      </div>
      <div class="side-content">
        @foreach( $recommended as $entry)
          <div class="article-box">
            @if ($entry['type']=='github')
              @if ($entry['id']!='')
                <a href="{{ URL::to('/article/code/'.$entry['type'].'?id='.$entry['id']) }}">
              @else
                <a href="{{ URL::to('/article/code/'.$entry['type'].'?username='.urlencode($entry['username']).'&repo='.urlencode($entry['repo']).'&path='.urlencode($entry['path'])) }}">
              @endif
            @elseif ($entry['type']=='pocket')
              @if (isset($entry['video']))
                <a href="{{ URL::to('/article/video/'.$entry['type'].'?id='.$entry['id']) }}">
              @else
                <a href="{{ URL::to('/article/image/'.$entry['type'].'?id='.$entry['id']) }}">
              @endif
            @elseif ($entry['type']=='vimeo')
                @if (isset($entry['id']))
                  <a href="{{ URL::to('/article/video/'.$entry['type'].'?id='.$entry['id']) }}">
                @elseif (isset($entry['tag']))
                  <a href="{{ URL::to('/article/video/'.$entry['type'].'?id='.$entry['url'].'&tag='.$entry['tag']) }}">
                @endif
            @elseif ($entry['type']=='slideshare')
                @if (isset($entry['id']))
                  <a href="{{ URL::to('/article/video/'.$entry['type'].'?id='.$entry['id']) }}">
                @elseif (isset($entry['tag']))
                  <a href="{{ URL::to('/article/video/'.$entry['type'].'?id='.$entry['url'].'&tag='.$entry['tag']) }}">
                @endif
            @endif
            <section class="image"
              @if (isset($entry['image']))
                style="background-image: url('{{ $entry['image'] }}')"
              @else
                style="background-image: url('{{ asset('img/articles/'.$entry['type'].'.jpg') }}')"
              @endif
            ></section>
            </a>
            @if (isset($entry['type']))
              <section class="tag">{{$entry['type']}}</section>
            @endif
            <section class="content">
              <h2>{{$entry['title']}}</h2>
              <!-- <label>@if (isset($entry['repo'])) {{$entry['repo']}} @endif</label> -->
              <label>@if (isset($entry['details'])) {{$entry['details']}} @endif</label>
              <p class="description">
                @if (isset($entry['description']))
                  {{$entry['description']}}
                @else
                  <!-- Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. -->
                @endif
              </p>
            </section>
            @if ($entry['type']=='github')
              @if ($entry['id']!='')
                <a href="{{ URL::to('/article/code/'.$entry['type'].'?id='.$entry['id']) }}">
              @else
                <a href="{{ URL::to('/article/code/'.$entry['type'].'?username='.urlencode($entry['username']).'&repo='.urlencode($entry['repo']).'&path='.urlencode($entry['path'])) }}">
              @endif
            @elseif ($entry['type']=='pocket')
              @if (isset($entry['video']))
                <a href="{{ URL::to('/article/video/'.$entry['type'].'?id='.$entry['id']) }}">
              @else
                <a href="{{ URL::to('/article/image/'.$entry['type'].'?id='.$entry['id']) }}">
              @endif
            @elseif ($entry['type']=='vimeo')
                @if (isset($entry['id']))
                  <a href="{{ URL::to('/article/video/'.$entry['type'].'?id='.$entry['id']) }}">
                @elseif (isset($entry['tag']))
                  <a href="{{ URL::to('/article/video/'.$entry['type'].'?id='.$entry['url'].'&tag='.$entry['tag']) }}">
                @endif
            @elseif ($entry['type']=='slideshare')
                @if (isset($entry['id']))
                  <a href="{{ URL::to('/article/video/'.$entry['type'].'?id='.$entry['id']) }}">
                @elseif (isset($entry['tag']))
                  <a href="{{ URL::to('/article/video/'.$entry['type'].'?id='.$entry['url'].'&tag='.$entry['tag']) }}">
                @endif
            @endif
            <button type="button" name="view-btn" class="article-button">Read</button>
            </a>
          </div>
        @endforeach
        @for ($i = 0; $i < 2; $i++)

        @endfor
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
  <script src="{{ asset('js/tutorial.js') }}"></script>
@endsection
