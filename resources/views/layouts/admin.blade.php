<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin Activwa') }}</title>

   <!-- Scripts -->
    <script src="{{ asset('/assets/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('/assets/js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/assets/css/waku.css') }}" rel="stylesheet"> 

    <!-- Font Awesome 4.7 -->
    <link href="{{ asset('/assets/Font-Awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet">

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

</head>
<body>

    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    Admin Page
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
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
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $(".alert").delay(5000).fadeOut(5000);
        });
    </script>

</body>
</html>
