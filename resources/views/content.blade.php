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
          <li><a href="{{ URL::to('/recommended') }}">Recommended Content</a></li>
          <li><a href="">More</a></li>
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
    <div class="filter-container">
      <div class="filters">
        <div>Filters:</div>
        <span>Sort by</span>
        <span>Tags</span>
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
    <div class="content-container">
      @if (isset($content))
        @foreach ($content as $entry)
            <div class="article-box">
              <section class="image"
                @if (isset($entry['image']))
                  style="background-image: url('{{ $entry['image'] }}')"
                @else
                  style="background-image: url('{{ asset('img/articles/'.$entry['type'].'.jpg') }}')"
                @endif
              ></section>
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
                  <a href="{{ URL::to('/article/code/'.$entry['type'].'?id='.$entry['id']) }}">
                @else
                  <a href="">
                @endif
                <button type="button" name="view-btn" class="article-button">Read</button>
              </a>
            </div>
        @endforeach
      @else
        <div class="article-warning">
          <p>Warning: You don't have any content available!</p>
          <button type="button" name="view-btn" class="article-button fixed-size" data-toggle="modal" data-target="#addModal"><Add></Add>Add account</button>
        </div>
        @include ('modals.attach-account')
        @for ($i = 4; $i < 4; $i++)
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
              <a href="
              @if ($i%4==0)
                {{ URL::to('/article/code')}}
              @elseif ($i%4==1)
                {{ URL::to('/article/image')}}
              @elseif ($i%4==2)
                {{ URL::to('/article/image')}}
              @else
                {{ URL::to('/article/video')}}
              @endif
              ">
                <button type="button" name="view-btn" class="article-button">Read</button>
              </a>
            </div>
        @endfor
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
@endsection
