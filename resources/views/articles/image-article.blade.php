@extends('layouts.article')

@section('article-title')
  Image Article
@endsection

@section('article-content')
  @if(isset($content))
    @if (isset($content['image']))
      <section class="image" style="background-image: url('{{$content['image']}}');">
      </section>
    @else
      <section class="image" style="background-image: url('{{ asset('img/articles/'.$content['type'].'-hq.jpg') }}');">
      </section>
    @endif
    <section class="title">
      @if (isset($content['title']))
        <h2>{{$content['title']}}</h2>
      @endif
      @if (isset($content['type']))
      <section class="tag">{{$content['type']}}</section>
      @endif
    </section>
    <section class="details">
      @if (isset($content['authors']))
        <br><label>By </label>
        @foreach ($content['authors'] as $author)
          <label><span><a href={{$author[1]}} target="_blank">{{$author[0]}}</a></span></label>
        @endforeach
      @endif
    </section>
    <section class="content">
      @if (isset($content['description']))
        <p>
          {{$content['description']}}
        </p>
        @if (isset($content['url_content']))
          <label><span><a href={{$content['url_content']}} target="_blank">Article link</a></span></label>
        @endif
      @endif
    </section>
  @endif
@endsection
