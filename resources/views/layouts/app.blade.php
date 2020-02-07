<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Activwa') }}</title>

   <!-- Scripts -->
    <script src="{{ asset('/assets/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('/assets/js/app.js') }}"></script>
    <script src="{{ asset('/assets/datetimepicker/jquery.datetimepicker.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/assets/css/waku.css') }}" rel="stylesheet"> 
    <link href="{{ asset('/assets/datetimepicker/jquery.datetimepicker.css') }}" rel="stylesheet">

    <!-- Font Awesome 4.7 -->
    <link href="{{ asset('/assets/Font-Awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet">

    <!-- Icomoon -->
    <link href="{{ asset('/assets/icomoon/icomoon.css') }}" rel="stylesheet" />

     <!-- Emoji -->
    <link href="{{ asset('/assets/emoji/css/emojionearea.min.css') }}" rel="stylesheet"> 
    <script type="text/javascript" src="{{ asset('/assets/emoji/js/prettify.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/emoji/js/emojionearea.js') }}"></script>

    <!-- Data Table -->
    <link href="{{ asset('/assets/DataTables/datatables.min.css') }}" rel="stylesheet">
    <script defer type="text/javascript" src="{{ asset('/assets/DataTables/datatables.min.js') }}"></script>

    <!-- CKEditor -->
    <link href="{{ asset('/assets/ckeditor/contents.css') }}" rel="stylesheet" />
    <script type="text/javascript" src="{{ asset('/assets/ckeditor/ckeditor.js') }}"></script>

    <!-- CKFinder -->
    <script type="text/javascript" src="{{ asset('/assets/ckfinder/ckfinder.js') }}"></script>

    <!-- Datetimepicker -->
    <link href="{{ asset('/assets/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/assets/datetimepicker/js/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script> 

    <!-- MDtimepicker -->
    <link href="{{ asset('/assets/MDTimePicker/mdtimepicker.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/assets/MDTimePicker/mdtimepicker.min.js') }}"></script>

    <!-- Clipboard -->
    <script type="text/javascript" src="{{ asset('/assets/clipboard.js-master/clipboard.min.js') }}"></script> 

    <!-- Main Styles -->
    <link href="{{ asset('/assets/css/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/css/dashboard.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/css/lists.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/css/campaign.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/css/settings.css') }}" rel="stylesheet" />

    <!-- Jquery Tabs -->
    <link href="{{ asset('/assets/css/jquery-tabs.css') }}" rel="stylesheet" />
    <!-- Jquery Custom Select -->
    <link href="{{ asset('/assets/css/custom-select.css') }}" rel="stylesheet" />

</head>
<body>

    <!-- Loading lama
    <div id="div-loading">
      <div class="loadmain"></div>
      <div class="background-load"></div>
    </div>
    -->
    <!--Loading Bar-->
    <div class="div-loading">
      <div id="loader" style="display: none;"></div>  
    </div> 

    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-telegram header-tel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('home') }}">
                    <img class="logo" src="{{asset('assets/img/logo.png')}}" />
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav">
                        <!-- Authentication Links -->
                        @guest
                            <!--
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                          -->
                        @else
                            <li class="nav-item dropdown">
                                 <a id="navbarDropdown" class="nav-link {{ (request()->is('home') || request()->is('createlists') || request()->is('create-campaign')) ? 'active' : '' }} dropdown-toggle" href="{{ route('home') }}" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                     Create<span class="caret"></span>
                                </a>
                                
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a href="{{url('createlists')}}" class="nav-link {{ (request()->is('createlists')) ? 'active' : '' }}">Lists</a>
                                    
                                    <a href="{{url('create-campaign')}}" class="nav-link {{ (request()->is('create-campaign')) ? 'active' : '' }}">Campaigns</a> 
                                </div>
                            </li> 

                            <li class="nav-item">
                                <a href="{{url('lists')}}" class="nav-link {{ (request()->is('lists') || request()->is('lists-create')) ? 'active' : '' }}">Lists</a>
                            </li> 

                            <!--
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('lists')) ? 'active' : '' }}" href="{{url('lists')}}">Lists</a>
                            </li> 
                            -->

                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('campaign') || request()->is('add-reminder')) ? 'active' : '' }}" href="{{url('campaign')}}">Campaigns</a>
                            </li> 
                            <!--
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('home')) ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                            </li> 
                            <li class="nav-item">
                                <a class="nav-link {{ (Request::segment(1) == 'usercustomer') || (request()->is('userlist')) || (request()->is('createlist')) ? 'active' : '' }}" href="{{route('userlist')}}">Lists & Events</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('broadcast')||request()->is('broadcast_customer')||request()->is('broadcastform')||request()->is('broadcasteventform')) ? 'active' : '' }}" href="{{ route('broadcast') }}">Broadcast</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('reminder')||request()->is('reminderform')||request()->is('reminder_customer')||request()->is('reminderautoreply')) ? 'active' : '' }}" href="{{ route('reminder') }}">Reminder List</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('event')||request()->is('eventform')||request()->is('eventcustomer')||request()->is('eventautoreply')) ? 'active' : '' }}" href="{{ route('event') }}">Reminder Event</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('templates')) ? 'active' : '' }}" href="{{ route('templates') }}">Template</a>
                            </li> 
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('devices')) ? 'active' : '' }}" href="{{ route('devices') }}">Devices</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                          -->
                    </ul>
                    <ul class="navbar-nav mr-auto"><!-- separator --></ul>
                     <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item mr-3">
                           Hi,
                        </li>
                        <li class="nav-item mr-3">
                            User
                            <!--
                           <select class="form-control-sm tel-color" name="">
                             <option>08523193113</option>
                           </select>
                            -->
                        </li>
                         <li class="nav-item cog-pos dropdown">
                           <a id="cogDropdown" class="icon-cog" data-toggle="dropdown"></a>
                           <div class="dropdown-menu dropdown-menu-right text-center" aria-labelledby="cogDropdown">
                                    <a href="{{url('settings')}}" class="nav-link {{ (request()->is('settings')) ? 'active' : '' }}">Settings</a>
                                    <a href="{{url('lists-create')}}" class="nav-link {{ (request()->is('lists-create')) ? 'active' : '' }}">Buy More</a>
                                    <a href="{{url('history-order')}}" class="nav-link {{ (request()->is('history-order')) ? 'active' : '' }}">History Order</a> 

                                    <a class="nav-link" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Log Out') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                            </div>
                        </li>
                    </ul>
                    @endguest

                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- footer -->
    <div class="col-md-12">
      <div class="container footer">
        Copyright &copy; 2020 <b>Activtele</b> All Rights Reserved.
      </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $(".alert-success,.alert-warning,.alert-danger").delay(5000).fadeOut(3000);
        });
        $(window).on('load', function() { 
          $("#div-loading").hide();
        });   
    </script>

</body>
</html>
