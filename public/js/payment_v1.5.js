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

function execPurchase(resultCode, errMsg) {
    if (resultCode !== "Success") {
        alert("3Dセキュア認証失敗: " + errMsg);
    } else {
        // Laravelへ送信するformを動的に生成
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/payment/confirm'; // ← Laravel側ルート

        const params = {
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            tkn: $("#tkn").val(),
            cvv: $("#cvv").val(),
            aid: '131580',
            am: $("#am").val(),
            tx: $("#tx").val(),
            sf: $("#sf").val(),
            em: $("#em").val(),

            // ✅ 追加項目
            cod: $("#cod").val(),         // 商品コード（事前に hidden で入れておく）
            fn: $("#fn").val(),
            ln: $("#ln").val(),
            jb: "CAPTURE",
            rt: "0"
        };


        for (const key in params) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = params[key];
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    }
}


