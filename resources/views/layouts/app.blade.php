<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Waku') }}</title>

   <!-- Scripts -->
    <script src="{{ asset('/assets/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('/assets/js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/assets/css/waku.css') }}" rel="stylesheet">

     <!-- Emoji -->
    <link href="{{ asset('/assets/emoji/css/emojionearea.min.css') }}" rel="stylesheet"> 
    <script type="text/javascript" src="{{ asset('/assets/emoji/js/prettify.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/emoji/js/emojionearea.js') }}"></script>

    <!-- Data Table -->
    <link href="{{ asset('/assets/DataTables/datatables.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/assets/DataTables/datatables.min.js') }}"></script>

    <!-- CKEditor -->
    <link href="{{ asset('/assets/ckeditor/contents.css') }}" rel="stylesheet" />
    <script type="text/javascript" src="{{ asset('/assets/ckeditor/ckeditor.js') }}"></script>

    <!-- CKFinder -->
    <script type="text/javascript" src="{{ asset('/assets/ckfinder/ckfinder.js') }}"></script>

</head>
<body>

    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
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
                                <a class="nav-link {{ (request()->is('userlist')) ? 'active' : '' }}" href="{{route('userlist')}}">Lists</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('broadcast')||request()->is('broadcast_customer')||request()->is('broadcastform')) ? 'active' : '' }}" href="{{ route('broadcast') }}">Broadcast</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('reminder')||request()->is('reminderform')||request()->is('reminder_customer')) ? 'active' : '' }}" href="{{ route('reminder') }}">Reminder</a>
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
         /* CKEditor */
        var editor = CKEDITOR.replace( 'editor1',{
            extraPlugins: 'filebrowser',
            extraPlugins: 'colorbutton',
        });
        CKFinder.setupCKEditor( editor );

        CKEDITOR.editorConfig = function( config ) {
            config.extraPlugins = 'filebrowser';
            config.extraPlugins = 'colorbutton';
        };

        $(document).ready(function(){
            $(".alert").delay(2000).fadeOut(3000);
        });
    </script>

</body>
</html>
