<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Asistencias') }}</title>
    <link href="{{ asset('storage/dev-icon.ico') }}" rel="shortcut icon" type="image/x-icon" />
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->

    {{-- <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"> --}}
    {{-- <link rel="stylesheet" type="text/css" href="css/fontawesome-all.min.css"> --}}
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.2/css/all.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bronze-theme.css') }}">
    @yield('css')
</head>

<body>
    <div id="app">
        <div class="menu-toggle">
            <div class="icon"></div>
        </div>
        <div class="main-menu">
            @guest
                <div class="menu-links">
                    <li>
                        <a href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li>
                            <a href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                </div>
            @else
                <div class="contant-info">
                    <div><a href="{{ url('/users/profile') }}">{{ Auth::user()->name }}</a></div>
                    {{-- <div>ADMINISTRADOR</div> --}}
                </div>
                <div class="menu-links">
                    <ul>
                        <li><a href="{{ url('/') }}">Inicio</a></li>
                        <!--<li><a href="{{ url('/home') }}">Sistema</a></li>-->
                        @if (Auth::user()->hasGroupPermission('viewRGerencia'))
                            <!--<li>-->
                            <!--    <a class="nav-link" data-target="#carouselExampleFade" data-slide-to="1">Lista</a>-->
                            <!--</li>-->
                            <li>
                                <a class="nav-link" data-target="#carouselExampleFade" data-slide-to="2">Reporte</a>
                            </li>
                        @endif
                        <li><a href="{{ route('logout') }}"
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a></li>
                    </ul>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    {{-- </li> --}}
                </div>
            @endguest

            {{-- <div class="social-media">
                <div class="social-link-holder"><a href="#">Dribbble</a></div>
                <div class="social-link-holder"><a href="#">Instagram</a></div>
                <div class="social-link-holder"><a href="#">Twitter</a></div>
                <div class="social-link-holder"><a href="#">Facebook</a></div>
            </div> --}}
        </div>
        <nav class="container-fluid cnav">
            <div class="row">
                <div class="col">
                    <div class="logo-holder">
                        <a href="{{ url('/') }}"><img class="logo" src="images/logo.png" alt="BETA"></a>
                    </div>
                </div>
                <div class="col text-right">
                    <div class="social-media">
                        <div class="social-link-holder"><a href="javascript:void(null)">Trabajamos con la tecnologia a
                                la mano</a></div>
                        <!--<div class="social-link-holder"><a href="javascript:void(null)">Contacto del desarrollador</a></div>-->
                        {{-- <div class="social-link-holder"><a href="#">Twitter</a></div>
                        <div class="social-link-holder"><a href="#">Facebook</a></div> --}}
                    </div>
                </div>
            </div>
        </nav>
        <header class="container-fluid header">
            <div class="mouse-scroll"></div>
            <div class="row">
                <div class="col">
                    <div class="extra-lg-text">
                        <span>Sistema de</span><br>
                        <span>Asistencia</span><br>
                        {{-- <span>EZ</span><br> --}}
                        <span class="other-color">EZ-BETA</span>
                    </div>
                </div>
            </div>
        </header>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <footer class="container-fluid footer">
        <div class="row">
            <div class="col">
                <div class="lg-text">
                    <span>100% facil de usar.</span><br>
                    <span>Desarrollado por Beta para Beta.</span>
                </div>
                {{-- <div class="normal-text">
                    <p><br>strategies, latest technologies and friendly creatives that<br>will work to produce the best outcome possible.</p>
                </div> --}}
            </div>
        </div>
        <!--<div class="row">-->
        <!--    <div class="col">-->
        <!--        <div class="contact-info-holder">-->
        <!--            <div class="title">Desarrollador</div>-->
        <!--            <div class="contact-info">+51 918285752</div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--<div class="col">-->
        <!--    <div class="contact-info-holder">-->
        <!--        <div class="title">E-mail</div>-->
        <!--<div class="contact-info"><a href="mailto:aguado.soft2016@gmail.com">aguado.soft2016</a></div>-->
        <!--    </div>-->
        <!--</div>-->
        <!--</div>-->
    </footer>
    {{-- <script src="js/jquery.min.js"></script> --}}
    {{-- <script src="js/popper.min.js"></script> --}}
    {{-- <script src="js/bootstrap.min.js"></script> --}}
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous">
    </script>
    <script src="{{ asset('assets/js/anime.min.js') }}"></script>
    <script src="{{ asset('assets/js/scrollreveal.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @yield('js')
</body>

</html>
