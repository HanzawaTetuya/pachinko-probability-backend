<!DOCTYPE html>
<html lang="jp">

<head>
    @include('admin.global.head')
</head>

<body>

    @include('admin.global.dashboard.sidebar')

    <div id="main-content-wrapper">

        @yield('content')

        @stack('scripts')
        
    </div>
</body>

</html>