<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>RiX - @yield('title')</title>
        <meta name="description" content="">
        <meta name="keywords" content="resource, interactive, explorer">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="{{ asset('css/normalize.min.css') }}" type="text/css">
        <link rel="stylesheet" href="{{ asset('css/default.css') }}" type="text/css">
        <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}" type="text/css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
        @yield('styles')

        <!--[if lt IE 9]>
            <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <script>window.html5 || document.write('<script src="js/vendor/html5shiv.js"><\/script>')</script>
        <![endif]-->
    </head>
    <body>

      <!--[if lt IE 8]>
          <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
      <![endif]-->
      <div class="loader">
          <i class="fa fa-cog fa-spin fa-5x fa-fw margin-bottom"></i>
      </div>

      <div class="main-wrapper">
        @yield('navigation')

        <div class="container-fluid">
          @yield('content')
        </div>
      </div>



      @yield('scripts')

    </body>
</html>