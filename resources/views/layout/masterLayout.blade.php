<html>
    <head>
        @include('templates.header')
        @yield('header')
    </head>
    <body id="pageBody">
        @yield('content')
        @include('templates.navbar')
        @include('templates.scripts')
        @yield('scripts')
    </body>
</html>
