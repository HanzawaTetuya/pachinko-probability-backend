
document.addEventListener('DOMContentLoaded', function () {
    const togglePasswordVisibility = document.getElementById('toggle-password-visibility');
    const passwordInput = document.getElementById('password');
    const authCodeInput = document.getElementById('auth-code');
    const comfirmPasswordInput = document.getElementById('password-confirm');

    // データ属性から画像のパスを取得
    const seeImg = togglePasswordVisibility.getAttribute('data-see-img');
    const noSeeImg = togglePasswordVisibility.getAttribute('data-no-see-img');

    
    // パスワード表示/非表示の切り替え機能
    togglePasswordVisibility.addEventListener('click', function () {
        const isPasswordHidden = passwordInput.type === 'password';

        if (isPasswordHidden) {
            passwordInput.type = 'text';
            togglePasswordVisibility.src = noSeeImg; // アイコンを非表示のものに変更
        } else {
            passwordInput.type = 'password';
            togglePasswordVisibility.src = seeImg; // アイコンを表示のものに変更
        }
    });
    togglePasswordVisibility.addEventListener('click', function () {
        const isPasswordHidden = comfirmPasswordInput.type === 'password';

        if (isPasswordHidden) {
            comfirmPasswordInput.type = 'text';
            togglePasswordVisibility.src = "{{ asset('img/no-see.svg') }}"; // アイコンを非表示のものに変更
        } else {
            comfirmPasswordInput.type = 'password';
            togglePasswordVisibility.src = "{{ asset('img/see.svg') }}"; // アイコンを表示のものに変更
        }
    });
    togglePasswordVisibility.addEventListener('click', function () {
        const isPasswordHidden = authCodeInput.type === 'password';

        if (isPasswordHidden) {
            authCodeInput.type = 'text';
            togglePasswordVisibility.src = "{{ asset('img/no-see.svg') }}"; // アイコンを非表示のものに変更
        } else {
            authCodeInput.type = 'password';
            togglePasswordVisibility.src = "{{ asset('img/see.svg') }}"; // アイコンを表示のものに変更
        }
    });
    togglePasswordVisibility.addEventListener('click', function () {
        const isPasswordHidden = comfirmPasswordInput.type === 'password';

        if (isPasswordHidden) {
            comfirmPasswordInput.type = 'text';
            togglePasswordVisibility.src = "{{ asset('img/no-see.svg') }}"; // アイコンを非表示のものに変更
        } else {
            comfirmPasswordInput.type = 'password';
            togglePasswordVisibility.src = "{{ asset('img/see.svg') }}"; // アイコンを表示のものに変更
        }
    });


});
