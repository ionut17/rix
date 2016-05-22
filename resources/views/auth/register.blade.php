@extends('layouts.auth')

@section('auth-title','Register')

@section('auth-content')

  <figure class="logo small">
    <img src="{{asset('img/rix_logo.svg')}}" draggable="false"/>
    <label for="">Resource Interactive Explorer</label>
  </figure>
  <form action="{{ URL::to('/register_authorize') }}" method="post">
    {{ csrf_field() }}
    @if (isset($error))
      <label>{{$error}}</label>
    @endif
    <div class="box">
        <label for="username">Username</label>
        <input type="text" name="username" id="username">
        <label for="username">Email Address</label>
        <input type="text" name="email" id="email">
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
        <label for="rpassword">Repeat Password</label>
        <input type="password" name="rpassword" id="rpassword">
    </div>
    <button type="submit" class="button">
      Register
    </button>
  </form>
  <label class="box-label"> Have an account? <a href="{{ URL::to('/login') }}">Login now</a></label>

@endsection
