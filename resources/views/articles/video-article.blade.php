@extends('layouts.article')

@section('article-title')
Video Article
@endsection

@section('article-content')
<section class="title">
  @if (isset($content['title']))
  <h2>{{ $content['title'] }}</h2>
  @endif
  @if(isset($content['type']))
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
<section class="video">
  @if (isset($content['content']))
  <section>{!! $content['content'] !!}</section>
  @endif
  @if (isset($content['video']))
    <section>
      <iframe width="560" height="315" src="{!! $content['video'] !!}" frameborder="0" allowfullscreen></iframe>
    </section>
  @endif
</section>
<section class="content">
  {{ $content['description'] }}
</section>
@endsection
