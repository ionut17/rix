<nav class="navigation-wrapper">
  <div class="container">
    <div class="navigation-container">
      <a href="{{ URL::to('/mycontent') }}" style="text-decoration:none;">
        <figure class="logo small">
          <img src="{{asset('img/rix_logo.svg')}}" draggable="false"/>
          <label for="">Resource Interactive Explorer</label>
        </figure>
      </a>
      <ul class="nav-items">
        <!-- <li><a href="{{ URL::to('/settings') }}"><figure class="profile-picture" style="background-image: url('{{asset('img/profiles/avatar.jpg')}}');"></figure></a></li> -->
        <li>
          <a href="{{ URL::to('/settings') }}">
          <i class="fa fa-cog" aria-hidden="true"></i>
            Settings
          </a>
        </li>
        <li>
          <a href="{{ URL::to('/logout') }}">
          <i class="fa fa-sign-out" aria-hidden="true"></i>
            Log out
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
