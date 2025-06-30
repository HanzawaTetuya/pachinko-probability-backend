
document.addEventListener('DOMContentLoaded', function () {
    const togglePasswordVisibility = document.getElementById('toggle-password-visibility');
    const passwordInput = document.getElementById('password');
    const togglePasswordComfirmVisibility = document.getElementById('toggle-password-confirm-visibility');
    const passwordComfirmInput = document.getElementById('password-confirm');
    
    // パスワード表示/非表示の切り替え機能
    togglePasswordVisibility.addEventListener('click', function () {
        const isPasswordHidden = passwordInput.type === 'password';

        if (isPasswordHidden) {
            passwordInput.type = 'text';
            togglePasswordVisibility.src = '../img/no-see.svg'; // アイコンを非表示のものに変更
        } else {
            passwordInput.type = 'password';
            togglePasswordVisibility.src = '../img/see.svg'; // アイコンを表示のものに変更
        }
    });

    togglePasswordComfirmVisibility.addEventListener('click', function () {
        const isPasswordcomfirmHidden = passwordComfirmInput.type === 'password-confirm';

        if (isPasswordcomfirmHidden) {
            passwordComfirmInput.type = 'text';
            togglePasswordVisibility.src = '../img/no-see.svg'; // アイコンを非表示のものに変更
        } else {
            passwordComfirmInput.type = 'password';
            togglePasswordVisibility.src = '../img/see.svg'; // アイコンを表示のものに変更
        }
    });
});
