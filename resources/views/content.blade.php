@extends('layouts.master')

@section('title')
  My Content
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
        <section class="search">
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
    <div class="filter-container">
        <div class="filters">
          @if (Route::getCurrentRoute()->getPath()=='mycontent' || Route::getCurrentRoute()->getPath()=='mycontent/{page_number?}')
            <span id="filter-option">Filters</span>
          @endif
        </div>

      <ul class="pagination">
        @for ($i=1;$i<=$page_count;$i++)
          @if (isset($target))
            <li><a href="{{ URL::to('/'.$target.'/'.$i) }}" @if ($i==$page_number) class="selected" @endif>{{$i}}</a></li>
          @else
            <li><a href="{{ URL::to('/mycontent/'.$i) }}" @if ($i==$page_number) class="selected" @endif>{{$i}}</a></li>
          @endif
        @endfor
      </ul>
    </div>
    <div class="filter-content" id="filter-content" style="display: none;">
      <label class="tag">Filters:</label>
      <ul>
        <li>
          <input type="checkbox" name="github" id="github-opt" checked>
          <label for="github">Github</label>
        </li>
        <li>
          <input type="checkbox" name="pocket" id="pocket-opt" checked>
          <label for="pocket">Pocket</label>
        </li>
        <li>
          <input type="checkbox" name="slideshare" id="slideshare-opt" checked>
          <label for="slideshare">Slideshare</label>
        </li>
        <li>
          <input type="checkbox" name="vimeo" id="vimeo-opt" checked>
          <label for="vimeo">Vimeo</label>
        </li>
      </ul>
      <button type="button" name="view-btn" class="article-button" id="filter-save">Filter</button>
    </div>
    <div class="content-container">
      @if ($has_accounts==true)
        @if (isset($content))
          @foreach ($content as $entry)
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
                <section class="tag">
                  @if (isset($entry['type'])) {{$entry['type']}} @endif
                </section>
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
                      @elseif (isset($entry['tags']))
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
        @else
          <div class="article-warning">
            <p>Warning: You don't have any content available!</p>
            <!-- <button type="button" name="view-btn" class="article-button fixed-size" data-toggle="modal" data-target="#addModal"><Add></Add>Add account</button> -->
          </div>
        @endif
      @else
        <div class="article-warning">
          <p>Warning: You don't have any account connected!</p>
          <button type="button" name="view-btn" class="article-button fixed-size" data-toggle="modal" data-target="#addModal"><Add></Add>Add account</button>
        </div>
        @include ('modals.attach-account')
      @endif
    </div>
    <div class="footer-container">
      <ul class="pagination hide">
        @for ($i=1;$i<=$page_count;$i++)
          <li><a href="{{ URL::to('/mycontent/'.$i) }}" @if ($i==$page_number) class="selected" @endif>{{$i}}</a></li>
        @endfor
      </ul>
      <button type="button" class="hide">Load More</button>
    </div>
  </div>

@endsection

@section('footer')
  <div class="container-fluid" style="margin-top:20px;">
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
  @if (isset($show_tutorial))
    @if ($show_tutorial==true)
      <script src="{{ asset('js/tutorial.js') }}"></script>
    @endif
  @endif

@endsection
