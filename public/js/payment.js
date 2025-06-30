console.log("RobotPaymentConfig:", window.RobotPaymentConfig);

function doPurchase() {
    console.log("開始：トークン作成");

    CPToken.TokenCreate({
        aid: window.RobotPaymentConfig.aid,
        cn: $("#cn").val(),
        ed: $("#ed_year").val().slice(2) + $("#ed_month").val(),
        fn: $("#fn").val(),
        ln: $("#ln").val(),
        cvv: $("#cvv").val()
    }, function (resultCode, token, errMsg) {
        console.log("トークン作成結果:", resultCode);
        console.log("トークン型:", typeof token);
        console.log("トークン値:", token);
        console.log("エラー:", errMsg);

        // トークンが空文字・undefined/null の場合エラーにする
        if (resultCode !== "Success" || !token || token.trim() === "") {
            alert("トークン生成に失敗しました。内容が空です。エラー: " + (errMsg || "（なし）"));
            return;
        }

        $("#tkn").val(token);
        console.log("✅ トークンが正常にセットされました:", token);
        execAuth();
    });
}


function execAuth() {
    ThreeDSAdapter.authenticate({
        tkn: $("#tkn").val(),
        aid: window.RobotPaymentConfig.aid,
        am: $("#am").val(),
        tx: $("#tx").val(),
        sf: $("#sf").val(),
        em: $("#em").val()
    }, execPurchase);
}

function execPurchase(resultCode, errMsg) {
    if (resultCode !== "Success") {
        alert("3Dセキュア認証失敗: " + errMsg);
    } else {
        // 成功時はカード情報をクリア
        $("#cn").val("");
        $("#ed_year").val("");
        $("#ed_month").val("");
        $("#fn").val("");
        $("#ln").val("");
        $("#cvv").val("");
        $("#mainform").submit();
    }
}

