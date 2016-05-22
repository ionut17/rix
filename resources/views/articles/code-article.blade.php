@extends('layouts.article')

@section('article-title')
  Code Article
@endsection

@section('article-content')
  @if (isset($content))

    <section class="title">
      @if (isset($content['title']))
        <h2>{{$content['title']}}</h2>
      @endif
      @if (isset($content['tag']))
      <section class="tag">{{$content['type']}}</section>
      @endif
    </section>
    <section class="details">
      @if (isset($content['details']) && isset($content['url']))
        <label><span><a href="{{$content['url']}}" target="_blank">{{$content['details']}}</a></span></label>
      @elseif (isset($content['details']))
        <label><span>{{$content['details']}}</span></label>
      @endif
    </section>
    <section class="content">
      <p class="bold">
        <!-- Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
        Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. -->
      </p>
    </section>
    <section class="code">
      @if (isset($content['content']))
        {!!$content['content']!!}
      @endif
    </section>
  @endif
@endsection
