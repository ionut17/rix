@extends('layouts.auth')

@section('auth-title','Login')

@section('auth-content')

  <figure class="logo small">
    <img src="{{asset('img/rix_logo.svg')}}" draggable="false"/>
    <label for="">Resource Interactive Explorer</label>
  </figure>
  <form action="{{ URL::to('/login_authorize') }}" method="post">
    {{ csrf_field() }}
    @if (isset($error))
      <label>{{$error}}</label>
    @endif
    <div class="box">
      <label for="username">Username</label>
      <input type="text" name="username" id="username">
      <label for="password">Password</label>
      <input type="password" name="password" id="password">
      <input type="checkbox" name="remember" id="remember">
      <label for="remember">Remember me</label>
    </div>
    <button type="submit" class="button">
      Login
    </button>
  </form>
  <label class="box-label"> Don't have an account? <a href="{{ URL::to('/register') }}">Register now</a></label>

@endsection
