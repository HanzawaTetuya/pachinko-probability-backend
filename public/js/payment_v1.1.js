function doPurchase() {
    CPToken.TokenCreate({
        aid: '131580',
        cn: $("#cn").val(),
        ed: $("#ed_year").val().slice(2) + $("#ed_month").val(),
        fn: $("#fn").val(),
        ln: $("#ln").val(),
        cvv: $("#cvv").val()
    }, execAuth); // トークン生成後に3Dセキュアへ進む
}

function execAuth(resultCode, errMsg) {
    if (resultCode != "Success") {
        // 戻り値がSuccess以外の場合はエラーメッセージを表示します。
        window.alert(errMsg);
    } else {

        ThreeDSAdapter.authenticate({
            tkn: $("#tkn").val(),
            cvv: $("#cvv").val(),
            aid: '131580',
            am: $("#am").val(),
            tx: $("#tx").val(),
            sf: $("#sf").val(),
            em: $("#em").val()
        }, execPurchase);
    }
}

const isDebugMode = true; // ← false にすると通常送信

function execPurchase(resultCode, errMsg) {
    if (resultCode !== "Success") {
        alert("3Dセキュア認証失敗: " + errMsg);
    } else {
        $("#cn").val("");
        $("#ed_year").val("");
        $("#ed_month").val("");
        $("#fn").val("");
        $("#ln").val("");
        $("#cvv").val("");

        if (isDebugMode) {
            const form = document.getElementById("mainform");
            const formData = new FormData(form);
            const entries = {};
            for (let [key, value] of formData.entries()) {
                entries[key] = value;
            }

            console.log("🐞 [デバッグモード] 実送信せず、送信予定の内容:", entries);
            alert("デバッグモード中。送信内容をconsoleに出力しました。");
        } else {
            $("#mainform").submit(); // 本番だけ送信
        }
    }
}



