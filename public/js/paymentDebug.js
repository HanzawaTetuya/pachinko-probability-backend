console.log("Debugモード起動: paymentDebug.js読み込み成功");

function doPurchase() {
    // 必須チェック
    if (!$("#cn").val().trim()) {
        alert("カード番号を入力してください");
        return;
    }
    if (!$("#ed_month").val().trim() || !$("#ed_year").val().trim()) {
        alert("有効期限（月・年）を選択してください");
        return;
    }
    if (!$("#cvv").val().trim()) {
        alert("セキュリティコードを入力してください");
        return;
    }
    if (!$("#fn").val().trim()) {
        alert("カード名義（名）を入力してください");
        return;
    }
    if (!$("#ln").val().trim()) {
        alert("カード名義（姓）を入力してください");
        return;
    }

    // ここは実際にはRobotPaymentのトークン取得APIを叩くはずだが
    // デバッグなのでダミートークンを直接セット
    $("#tkn").val("dummy_token_123456");

    // 次のステップへ進む
    execAuth();
}

function execAuth() {
    console.log("【モック】3Dセキュア認証開始");

    // 本来はここでEMV3DS認証だが、デバッグなのでそのまま成功にする
    execPurchase("Success", "");
}

function execPurchase(resultCode, errMsg) {
    if (resultCode !== "Success") {
        alert("3Dセキュア認証失敗: " + errMsg);
    } else {
        console.log("【モック】3Dセキュア認証成功");

        $("#cn").val("");
        $("#ed_year").val("");
        $("#ed_month").val("");
        $("#fn").val("");
        $("#ln").val("");
        $("#cvv").val("");

        // ★ ここでsubmit先をhandleResultに
        $("#mainform").attr('action', '/payment/debug/notify');
        $("#mainform").submit();
    }
}