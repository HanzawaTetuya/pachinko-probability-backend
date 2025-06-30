<header id="header">
    <button id="toggle-sidebar">&#9776;</button>
    <div id="breadcrumbs">
        @yield('breadcrumbs') <!-- パンくずメニューを挿入するセクション -->
    </div>
    <div id="user-info">
        <!-- ログイン中のユーザーのメールアドレスの最初の1文字を大文字で表示 -->
        <span id="user-icon">{{ strtoupper(substr($admin->email, 0, 1)) }}</span>
        <!-- ログイン中のユーザーのメールアドレスを表示 -->
        <span id="user-email">{{ $admin->email }}</span>
        <img src="{{ asset('img/arrow 1.svg') }}" alt="">
        <div id="user-menu" class="hidden">
            <div class="info">
                <!-- ログイン中のユーザーのメールアドレスの最初の1文字を大文字で表示 -->
                <span id="user-icon">{{ strtoupper(substr($admin->email, 0, 1)) }}</span>
                <!-- ログイン中のユーザーのメールアドレスを表示 -->
                <span id="user-email">{{ $admin->email }}</span>

                <a href="{{ route('admin.mypage.login') }}">My Profile</a>

            </div>
            <div class="login-now">
                <!-- ログイン中のユーザーのメールアドレスの最初の1文字を大文字で表示 -->
                <span id="user-icon">{{ strtoupper(substr($admin->email, 0, 1)) }}</span>
                <!-- ログイン中のユーザーのメールアドレスを表示 -->
                <span id="user-email">{{ $admin->email }}</span>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit">
                    <img src="{{ asset('img/see.svg') }}" alt="アイコン" class="button-icon">Logout
                </button>
            </form>
        </div>
    </div>
</header>