<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- フォントのリンク -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/user/thanksPage.css') }}">
    <title>ご購入ありがとうございました</title>
</head>

<body>
    <header>
        <img src="{{ asset('img/main-logo.png') }}" alt="ロゴ">
    </header>

    <main>
        <section class="top-section background">
            <p>ご購入ありがとうございます。</p>
            <p>購入した商品はすぐご利用いただけます。</p>
            <button onclick="redirectToAppNow()">今すぐアプリに戻る</button>
        </section>

        <section class="purchased-info background">
            <p class="title">購入情報</p>

            <div class="info-wrap">
                <p class="order-info">注文番号：{{ $orderNumber }}</p>

                <div class="products-wrap">
                    <p class="purchased-product-info">購入商品：</p>
                    @foreach ($products as $product)
                    <p>・{{ $product->name }}</p>
                    @endforeach
                </div>
            </div>

            <p class="price">合計金額：￥{{ number_format($amount) }}</p>
        </section>

        <section class="recommended-product">
            <p>おすすめ商品</p>

            <div class="product-wrap background">
                @if($recommendedProduct)
                <div class="product-up-wrap">
                    <p class="product-name">{{ $recommendedProduct->name }}</p>
                    <div class="product-info">
                        <p class="maker">メーカー：{{ $recommendedProduct->manufacturer }}</p>
                        <p class="genre">ジャンル：{{ $recommendedProduct->category }}</p>
                    </div>
                </div>
                <div class="product-bottom-wrap">
                    <p class="text">{{ Str::limit($recommendedProduct->description, 60, '...') }}</p>
                    <div class="product-bottom-buy">
                        <p class="price"><span>￥</span>{{ number_format($recommendedProduct->price) }}</p>

                    </div>
                </div>
                @else
                <p>おすすめ商品は現在ありません。</p>
                @endif
            </div>
        </section>

    </main>

    <footer></footer>

    <script>
        function redirectToApp() {
            const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

            if (isMobile && !isStandalone) {
                // スマホブラウザでアクセス中（ネイティブFlutterアプリ向け）
                window.location.href = 'yourapp://completed';
            } else {
                // PWA または Webブラウザ
                window.location.href = 'https://sigma-alpha.jp/#/completed'; // ← ローカル開発中はこれ
            }
        }

        // ✅ 5秒後にFlutterアプリ or Webにリダイレクト
        window.onload = function() {
            setTimeout(redirectToApp, 5000);
        };

        // ✅ 「今すぐ戻る」ボタン用
        function redirectToAppNow() {
            redirectToApp();
        }
    </script>


</body>

</html>