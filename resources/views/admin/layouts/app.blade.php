<!DOCTYPE html>
<html lang="jp">

<head>
    @include('admin.global.head')
</head>

<body>
    <div class="wrapper">
        @include('admin.global.header')

        @yield('content')

        @include('admin.global.footer')

        @stack('scripts')
    </div>
</body>

</html>