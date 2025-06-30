// scripts.js

// サイドバーのトグル機能
document.getElementById('toggle-sidebar').addEventListener('click', function () {
    document.body.classList.toggle('collapsed');
});

// ウィンドウのリサイズイベントを監視して、サイズに応じてサイドバーを収納
window.addEventListener('resize', function () {
    if (window.innerWidth < 1200) {
        document.body.classList.add('collapsed'); // 画面幅が1200px未満のときに自動的にサイドバーを収納
    } else {
        document.body.classList.remove('collapsed'); // 画面幅が1200px以上のときはサイドバーを表示
    }
});

// 初期ロード時にもチェック
if (window.innerWidth < 1200) {
    document.body.classList.add('smart_collapsed'); // 初期ロード時にサイドバーを収納
}

// メニューリンクとサブメニューのクリックイベントを設定
document.querySelectorAll('.menu-link').forEach(function (link) {
    link.addEventListener('click', function (event) {
        event.preventDefault(); // 通常のリンク動作を防止

        // 全てのno-submenuからアクティブクラスを削除
        document.querySelectorAll('.no-submenu').forEach(function (menu) {
            menu.classList.remove('active-menu');
        });

        // 全てのサブメニューアイテムからアクティブクラスを削除
        document.querySelectorAll('.submenu li').forEach(function (submenuItem) {
            submenuItem.classList.remove('active-submenu');
        });

        // 全てのサブメニューを閉じる
        document.querySelectorAll('.submenu').forEach(function (submenu) {
            submenu.style.display = 'none';
        });

        // クリックされたメニューがno-submenuの場合、アクティブクラスを追加
        let parentLi = link.closest('li.no-submenu');
        if (parentLi) {
            parentLi.classList.add('active-menu');
        }

        // クリックされたメニューがhas-submenuの場合、サブメニューを表示
        let submenuParent = link.closest('li.has-submenu');
        if (submenuParent) {
            let submenu = submenuParent.querySelector('.submenu');
            if (submenu) {
                submenu.style.display = 'block';
                let firstSubmenuItem = submenu.querySelector('li:first-child');
                if (firstSubmenuItem) {
                    firstSubmenuItem.classList.add('active-submenu');
                }
            }
        }
    });
});

// サブメニューのアイテムクリックで色を変更
document.querySelectorAll('.submenu li').forEach(function (submenuItem) {
    submenuItem.addEventListener('click', function (event) {
        event.stopPropagation();

        document.querySelectorAll('.submenu li').forEach(function (item) {
            item.classList.remove('active-submenu');
        });

        submenuItem.classList.add('active-submenu');
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const userInfo = document.getElementById('user-info');
    const userMenu = document.getElementById('user-menu');

    // #user-infoがクリックされたときにメニューの表示/非表示を切り替える
    userInfo.addEventListener('click', function () {
        userMenu.classList.toggle('visible');
    });
});