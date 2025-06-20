<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Админ</title>

    <link rel="stylesheet" href="{{asset('css/admin.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('css/news.css')}}">
    <link rel="stylesheet" href="{{asset('css/organizations.css')}}">
    <link rel="stylesheet"
        href="{{ asset('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- GidoAlert CSS -->
    <link rel="stylesheet" href="{{ asset('css/gido-alert.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat+Alternates:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Mukta:wght@200;300;400;500;600;700;800&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik+Mono+One&family=Unbounded:wght@200..900&display=swap" rel="stylesheet">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="preloader">
        <div class="preloader__row">
          <div class="preloader__item">
          </div>
          <div class="preloader__item">
            </div>
        </div>
      </div>
    <div class="wrapper">

        <nav style="background-color: #f4f4f4;border:none;" class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul style="" class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" data-auto-collapse-size="1920" href="#"
                        role="button" id="menu-toggle">
                        <div class="icon-container">
                            <svg class="menu-icon animated-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <line class="line line1" x1="4" y1="6" x2="20" y2="6"></line>
                                <line class="line line2" x1="4" y1="12" x2="20" y2="12"></line>
                                <line class="line line3" x1="4" y1="18" x2="20" y2="18"></line>
                            </svg>
                        </div>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block" >
                    <a style="color: #3752E9" href="{{route('admin.index')}}" class="nav-link podcherc">Админ панель</a>
               

                </li>
                <li class="nav-item d-none d-sm-inline-block" >
                   
                    <a style="color: #3752E9" href="{{route('welcome')}}" class="nav-link podcherc">Выход</a>

                </li>

            </ul>

            <ul style="margin-left:-5vw" class="navbar-nav ml-auto">
                <li class="nav-item">

                    <div class="navbar-search-block">
                        <form class="form-inline">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-navbar" type="search" placeholder="Search"
                                    aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>

                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button" id="fullscreen-toggle">
                        <div class="icon-container">
                            <svg class="fullscreen-icon animated-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path class="corner" d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                            </svg>
                        </div>
                    </a>
                </li>

            </ul>
        </nav>

        <aside style="background-color:  #3752E9;  border-radius: 0vw 1vw 1vw 0vw  " class="main-sidebar sidebar-primary elevation-4">

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                    <img style="width:2vw; height:2vw; margin-top: 0.5vw;" src="/img/personalAccount/avatar.png" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div  class="info">
                        <a style="color:#fff; margin-top: 1vw;font-size:12px" href="#" class="d-block">
                            {{ Auth::user()->name }}
                        </a>
                    </div>
                </div>
                <div>
                @include('admin.includes.sidebar')
                </div>
            </div>
        </aside>



        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                @yield('content')   
                </div>

            </div>
        </div>

        <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
        <script>
            $.widget.bridge('uibutton', $.ui.button)
        </script>
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
        <script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
        <script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
        <script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
        <script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
        
        <script src="{{ asset('js/gido-alert.js') }}"></script>
        
        <script src="/js/admin.js?time={{ time() }}"></script>
        <script src="{{ asset('dist/js/adminlte.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>
