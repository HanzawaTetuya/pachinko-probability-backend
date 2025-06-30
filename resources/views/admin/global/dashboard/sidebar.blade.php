<aside id="sidebar">
    <div class="logo-area">
        <img src="img/Growle_logo_black_width.png" alt="">
    </div>
    <!-- ここにサイドメニューの項目を追加 -->
    <nav>
        <ul class="nav-container">
            <li class="no-submenu">
                <a href="#home" class="menu-link">
                    <span class="menu-icon">🏠</span>
                    <span class="menu-text">ホーム</span>
                </a>
            </li>

            <li class="has-submenu">
                <a href="{{ route('show.products.login') }}" class="menu-link">
                    <div class="main-menu">
                        <span class="menu-icon">📦</span>
                        <span class="menu-text">商品管理</span>
                    </div>
                </a>
                <ul class="submenu">
                    <li><a href="{{ route('show.products.login') }}" class="submenu-link">商品一覧</a></li>
                    <li><a href="{{ route('show.products.login') }}" class="submenu-link">新規商品登録</a></li>
                    <li><a href="#開発中" class="submenu-link">新規セット商品登録</a></li>
                </ul>
            </li>

            <li class="has-submenu">
                <a href="#user-list" class="menu-link">
                    <div class="main-menu">
                        <span class="menu-icon">📋</span>
                        <span class="menu-text">登録者リスト</span>
                    </div>
                </a>
                <ul class="submenu">
                    <li><a href="{{ route('show.users.login') }}" class="submenu-link">登録者一覧</a></li>
                    <li><a href="#new-user" class="submenu-link">新規登録者</a></li>
                </ul>
            </li>

            <li class="has-submenu">
                <a href="#admin-list" class="menu-link">
                    <div class="main-menu">
                        <span class="menu-icon">🛠️</span>
                        <span class="menu-text">管理者リスト</span>
                    </div>
                </a>
                <ul class="submenu">
                    <li><a href="{{ route('show.admins.login') }}" class="submenu-link">管理者一覧</a></li>
                    <li><a href="#new-admin" class="submenu-link">新規管理者登録</a></li>
                </ul>
            </li>

            <li class="has-submenu">
                <a href="javascript:void(0)" class="menu-link">
                    <div class="main-menu">
                        <span class="menu-icon">📊</span>
                        <span class="menu-text">売上管理</span>
                    </div>
                </a>
                <ul class="submenu">
                    <li><a href="{{ route('show.sales.index') }}" class="submenu-link">売上管理</a></li>
                    <li><a href="#sales-report" class="submenu-link">売上レポート</a></li>
                </ul>
            </li>

        </ul>
    </nav>
</aside>