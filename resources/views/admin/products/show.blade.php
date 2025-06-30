@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/products/product_show.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/global/admin_dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/global/error_message.css') }}">
@endpush

@section('title','管理者ダッシュボード')

@section('breadcrumbs')
<a href="{{  route('admin.dashboard') }}">ホーム</a>　>　ログイン
@endsection

@section('content')

@include('admin.global.dashboard.header',['admin' => $admin])

<article id="main-content">
    <h2 class="section-title">商品詳細</h2>

    @if (session('success'))
    <div id="message-box" class="message-box success">
        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="8.5" cy="8.5" r="8.5" fill="currentColor" />
            <g clip-path="url(#clip0_61_223)">
                <path
                    d="M13.0848 3.7627C10.1777 5.37772 7.23487 9.97185 7.23487 9.97185L4.57929 6.95708L3 8.46442L6.83995 13.2376L8.23965 13.1839C10.0343 7.80008 14 4.19339 14 4.19339L13.0848 3.7627Z"
                    fill="white" />
            </g>
            <defs>
                <clipPath id="clip0_61_223">
                    <rect width="11" height="11" fill="white" transform="translate(3 3)" />
                </clipPath>
            </defs>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <section class="form-section-wrap">
        <div class="form-section">
            <div class="form-group-wrap">
                <div class="form-group">
                    <label for="product-image">機種画像</label>
                    <p>
                        @if ($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" style="max-width: 200px;">
                        @else
                        <img src="{{ asset('images/default-product.png') }}" alt="デフォルト画像" style="max-width: 200px;">
                        @endif
                    </p>
                </div>
                <div class="form-group">
                    <label for="product-number">商品コード</label>
                    <p>{{ $product->product_number }}</p>
                </div>
            </div>

            <!-- 商品名とメーカー名 -->
            <div class="form-group-wrap">
                <div class="form-group">
                    <label for="product-name">商品名</label>
                    <p>{{ $product->name }}</p>
                </div>
                <div class="form-group">
                    <label for="maker-name">メーカー名</label>
                    <p>{{ $product->manufacturer }}</p>
                </div>
            </div>

            <!-- カテゴリーと金額 -->
            <div class="form-group-wrap">
                <div class="form-group">
                    <label for="category">カテゴリー</label>
                    <p>{{ $product->category }}</p>
                </div>
                <div class="form-group">
                    <label for="price">金額</label>
                    <p>{{ number_format($product->price) }}円</p>
                </div>
            </div>

            <!-- 発売日とPythonファイルパス -->
            <div class="form-group-wrap">
                <div class="form-group">
                    <label for="release-date">発売日</label>
                    <p>{{ $product->release_date->format('Y年m月d日') }}</p>
                </div>
                <div class="form-group">
                    <label for="internal-storage">内部計算データ保存先</label>
                    <p>*****************************</p> <!-- セキュリティのためパスを隠す -->
                </div>
            </div>

            <!-- 商品説明 -->
            <div class="form-group-wrap">
                <div class="form-group-textarea">
                    <label for="product-description">商品説明</label>
                    <p>{{ $product->description }}</p>
                </div>
            </div>

            <!-- 公開設定スイッチ -->
            <div class="form-group-wrap switch-group">
                <div class="form-group">
                    <label for="public-setting">公開設定</label>
                    <p>{{ $product->is_published ? '公開' : '非公開' }}</p>
                </div>
            </div>

            <!-- 確認ボタン -->
            <div class="form-group-wrap group-btn">
                <div class="form-group-btn">

                    <form action="{{ route('product.edit.two.factor', ['id' => $product->id]) }}">
                        <button type="submit" class="add-product-btn">Edit</button>
                    </form>
                    <form action="{{ route('products.show') }}">
                        <button class="add-product-btn">戻る</button>
                    </form>

                </div>
            </div>
        </div>
    </section>
</article>

@endsection

<!-- JavaScript -->
@push('scripts')
<script src="{{ asset('js/scripts.js') }}"></script>
<script src="{{ asset('/js/input_password.js') }}"></script>
@endpush