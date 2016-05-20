@extends('layouts.article')

@section('article-title')
  Code Article
@endsection

@section('article-content')
  @if (isset($content))

    <section class="title">
      <h2>{{$content['name']}}</h2>
      <section class="tag">{{$content['type']}}</section>
    </section>
    <section class="details">
      <label><span>{{$content['repo']}}</span> / {{$content['path']}}</label>
    </section>
    <section class="content">
      <p class="bold">
        <!-- Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
        Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. -->
      </p>
    </section>
    <section class="code">
      {!!$content['content']!!}
    </section>
  @endif
@endsection
