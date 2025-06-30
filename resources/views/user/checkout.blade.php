<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- フォントのリンク -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">

    <script type="text/javascript" src="https://credit.j-payment.co.jp/gateway/js/jquery.js"></script>
    <script type="text/javascript" src="https://credit.j-payment.co.jp/gateway/js/CPToken.js"></script>
    <script type="text/javascript" src="https://credit.j-payment.co.jp/gateway/js/EMV3DSAdapter.js"></script>
    <script src="{{ asset('js/payment_v1.5.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/user/checkout.css?v=2.2') }}">
    <title>-システムα- 決済画面</title>
</head>

<body>
    <header>
        <img src="{{ asset('img/main-logo.png') }}" alt="ロゴ">
    </header>
    <main>

        <!-- <form id="mainform" method="POST" action="https://credit.j-payment.co.jp/gateway/gateway_token.aspx"> -->
            <form id="mainform" method="POST" action="{{ route('robot.confirm') }}">
            @csrf

            <!-- RobotPayment必要パラメータ（商品登録なし） -->
            <input type="hidden" name="aid" id="aid" value="{{ config('services.robotpayment.shop_id') }}">
            <input type="hidden" name="jb" id="jb" value="CAPTURE">
            <input type="hidden" name="rt" id="rt" value="0">
            <input type="hidden" name="cod" id="cod" value="{{ $order->order_number }}">
            <input type="hidden" name="tkn" id="tkn" value="">
            <input type="hidden" name="am" id="am" value="{{ intval($order->total_price) }}">
            <input type="hidden" name="tx" id="tx" value="0">
            <input type="hidden" name="sf" id="sf" value="0">
            <input type="hidden" name="em" id="em" value="{{ $email }}">

            <section class="cost-wrap">
                <div class="cost-text">
                    <div class="cost-text-up">
                        <p>請求金額</p>
                        <div>
                            <span>￥</span>
                            <p>{{ number_format($order->total_price) }}</p>
                        </div>
                    </div>
                    <p class="cost-text-down">
                        ご購入後はすぐにお使いいただけます。
                    </p>
                </div>
            </section>

            <section class="checkout-info-wrap">
                <p class="title">お支払情報</p>


                <div class="input-area-wrap">

                    <!-- 3Dセキュア -->
                    <div id="EMV3DS_INPUT_FORM"></div>

                    <!-- カード番号 -->
                    <div class="input-long-wrap">
                        <label class="input-label">カード番号<span>(必須)</span></label>
                        <input type="text" name="cn" id="cn" placeholder="例）1234567890123456（半角数字のみ）">
                    </div>

                    <p id="card-error" class="card-error-message" style="display: none;">このカードブランドは現在ご利用いただけません。</p>

                    <div class="card-brands">
                        <!-- <img src="/img/brands/visa.png" alt="visa" class="brand-logo brand-visa"> -->
                        <!-- <img src="/img/brands/mastercard.png" alt="mastercard" class="brand-logo brand-mastercard"> -->
                        <img src="/img/brands/amex.png" alt="amex" class="brand-logo brand-amex">
                        <img src="/img/brands/jcb.png" alt="jcb" class="brand-logo brand-jcb">
                        <img src="/img/brands/diners.png" alt="diners" class="brand-logo brand-diners">
                    </div>
                    <!-- 有効期限 -->
                    <div class="input-select-wrap">
                        <label class="input-label">有効期限<span>(必須)</span></label>
                        <div class="selector-wrap">
                            <select name="ed_month" id="ed_month">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                                    @endfor
                            </select>
                            <select name="ed_year" id="ed_year">
                                @for ($y = now()->year; $y <= now()->year + 10; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                            </select>
                        </div>
                    </div>
                    <div class="input-long-wrap">
                        <label class="input-label">セキュリティコード<span>(必須)</span></label>
                        <input type="text" id="cvv" name="cvv" placeholder="例）123（半角英数字のみ）">
                    </div>

                    <!-- 名義 -->
                    <div class="input-long-wrap">
                        <label class="input-label">カード名義（名）<span>(必須)</span></label>
                        <input type="text" name="fn" id="fn" placeholder="例）TARO（半角）">
                    </div>
                    <div class="input-long-wrap">
                        <label class="input-label">カード名義（姓）<span>(必須)</span></label>
                        <input type="text" name="ln" id="ln" placeholder="例）YAMADA（半角）">
                    </div>
                </div>
            </section>

            <section class="purchase-product-info-wrap">
                <p class="title">購入商品情報</p>
                <div class="product-wrap">
                    @foreach ($products ?? [] as $product)
                    <div class="product">
                        <p class="product-title">{{ $product->name }}</p>
                        <div class="product-menu">
                            <p class="maker">メーカー：{{ $product->manufacturer }}</p>
                            <p class="genru">ジャンル：{{ $product->category }}</p>
                        </div>
                        <div class="product-value">
                            <span>￥</span>
                            <p>{{ number_format($product->price) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- ボタン -->
            <section class="button">
                <input type="button" id="submit-btn" value="注文確定" class="currect-button" onclick="doPurchase()">

                <p>■ご購入前の注意事項と重要事項説明
                    ご購入手続き前に、以下の注意事項および重要事項を必ずご確認いただき、ご同意の上で「カートに追加」又は「注文確定」ボタンをクリックしてください。
                    ■商品について
                    本商品は「パチンコの確率計算ツール」です。ご購入後、専用ライセンスが発行され、デジタルコンテンツとして提供されます。
                    本ツール（以下、「当サービス」）は、システムアルファが統計学・論理学・確率論をもとに独自に計算した結果を提供するものです。利益や勝利を保証するものではありません。当サービスを使用した結果として生じた損害・損失に関しましては、一切責任を負いかねます。
                    ■ご利用条件・環境について
                    - ご利用はスマートフォンを推奨いたします。
                    - 購入時は安定したインターネット接続環境（通信状況の良い場所）でお手続きください。
                    - 商品の特性上、ご利用は18歳以上の方に限られます。
                    ■制限事項と免責事項について
                    - デジタルコンテンツのため、ご購入後の返品・返金・キャンセルは一切承ることができません。
                    - 商品に関するお問い合わせは専用フォームよりお願いいたします。カスタマーサービス対応時間は平日10時～19時、返信は1営業日以内を目安に対応いたします。
                    ■法律・著作権に関する注意事項
                    - 特定商取引法に基づく販売者情報、販売価格等の詳細は、ご購入画面に表示しておりますので必ずご確認ください。
                    - 個人情報の取扱いについては「プライバシーポリシー」を必ずご確認ください。
                    - 本商品の著作権および知的財産権は販売元に帰属します。無断での転載・複製・二次配布は法律により禁止されています。
                    ■お支払い方法とカード決済について
                    - お支払い方法はクレジットカード決済のみです。決済にはRobotPayment社を使用しております。決済時にエラーが表示された場合は、RobotPaymentの表示画面の指示に従いご対応ください。
                    - ご利用のカード情報は、不正利用の検知・防止のため、ご利用のカードの発行会社に提供される場合があります。カード発行会社が外国に所在する場合、当該外国へ情報提供されることがあります。当サービスにおいて発行会社およびその所在国を特定することは困難なため、詳細につきましてはお客様ご自身でご確認ください。外国所在の発行会社に対する個人情報保護に関する詳細は、「個人情報保護法に基づく情報提供」をご参照ください。
                    ■契約成立について
                    「注文確定」ボタンをクリックすることで、お客様は当サービスの利用規約、プライバシーポリシーおよび販売条件に同意の上でご注文されたことになります。価格や各種条件については必ずご購入画面および当ページをご確認ください。
                    注文が確定すると、決済完了通知がメールまたは決済完了画面に表示されます。この通知をもって売買契約が成立します。
                    ■お困りの場合
                    ご不明な点がある場合やお困りの際は、当サービスのヘルプページまたはカスタマーサービスからお問い合わせフォームをご利用下さい。</p>
            </section>
        </form>
    </main>
</body>

<script>
    window.RobotPaymentConfig = {
        aid: '{{ config("services.robotpayment.shop_id") }}'
    };
</script>
<script>
    const disallowedBrands = ['visa', 'mastercard']; // ←ここを ['visa', 'mastercard'] に変更するだけで即NG制御可能

    function detectCardBrand(number) {
        const cleaned = number.replace(/\D/g, '');

        const patterns = {
            visa: /^4[0-9]{0,}$/,
            mastercard: /^5[1-5][0-9]{0,}$/,
            amex: /^3[47][0-9]{0,}$/,
            diners: /^3(?:0[0-5]|[68][0-9])[0-9]{0,}$/,
            discover: /^6(?:011|5[0-9]{2})[0-9]{0,}$/,
            jcb: /^(?:2131|1800|35\d{0,})$/,
        };

        for (let brand in patterns) {
            if (patterns[brand].test(cleaned)) {
                return brand;
            }
        }

        return 'unknown';
    }

    const brandInput = document.getElementById('cn');
    const allBrands = document.querySelectorAll('.brand-logo');
    const errorMessage = document.getElementById('card-error');

    brandInput.addEventListener('input', function() {
        const brand = detectCardBrand(this.value);

        allBrands.forEach(logo => logo.classList.remove('active'));
        if (brand !== 'unknown') {
            const selected = document.querySelector(`.brand-${brand}`);
            if (selected) {
                selected.classList.add('active');
            }
        }

        const submitBtn = document.getElementById('submit-btn');

        if (disallowedBrands.includes(brand)) {
            errorMessage.style.display = 'block';
            submitBtn.disabled = true; // ← 無効化
            submitBtn.classList.add('disabled'); // CSS側でも視覚的に無効っぽくするなら
        } else {
            errorMessage.style.display = 'none';
            submitBtn.disabled = false; // ← 有効化
            submitBtn.classList.remove('disabled');
        }
    });
</script>







</html>