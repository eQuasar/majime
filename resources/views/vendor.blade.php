<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Majime - Vendor Backend Panel</title>
        
        <!-- <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css"> -->
        <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/custom.js') }}" defer></script>
    <script src="https://kit.fontawesome.com/ddbfc71eba.js" crossorigin="anonymous"></script>
    <meta name="user-id" content="{{ Auth::user()->id }}">
    <meta name="user-name" content="{{ Auth::user()->name }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;500;600;700;900&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    </head>
    <body>
    <div id="app">
        <div class="container-scroller">
            <app-header></app-header>
            <div class="container-fluid page-body-wrapper">
                <vendor-sidebar></vendor-sidebar>
                <main class="main-panel">
                    <div class="content-wrapper">
                    @yield('content')
                    <router-view :user="{{ Auth::user() }}"></router-view>
                    </div>
                </main>
            </div>
            <app-footer></app-footer>
        </div>
    </div>
</body>
</html>