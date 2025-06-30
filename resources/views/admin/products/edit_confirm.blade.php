@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/products/edit_confirm.css') }}">
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
    <h2 class="section-title">商品情報の確認</h2>
    <section class="form-section-wrap">
        <div class="form-section">
            <form action="{{ route('product.update', ['id' => $product->id]) }}" method="post">
                @csrf

                <!-- 機種画像 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="product-image">機種画像</label>
                        <p>
                            <img src="{{ asset('storage/' . session('product_image_path')) }}" alt="機種画像" style="max-width: 200px; height: auto;">
                        </p>
                    </div>
                </div>

                <!-- 商品名とメーカー名 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="product-name">商品名</label>
                        <p>{{ session('product_name', 'N/A') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="maker-name">メーカー名</label>
                        <p>{{ session('maker_name', 'N/A') }}</p>
                    </div>
                </div>

                <!-- カテゴリーと金額 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="category">カテゴリー</label>
                        <p>{{ session('category', 'N/A') }}</p>
                    </div>

                    <div class="form-group">
                        <label for="price">金額</label>
                        <p>{{ number_format(session('price', 0)) }}円</p>
                    </div>
                </div>

                <!-- 発売日と内部計算データ保存先 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="release-date">発売日</label>
                        <p>{{ session('release_date', 'N/A') }}</p>
                    </div>

                    <div class="form-group">
                        <label for="internal-storage">内部計算データ保存先</label>
                        <p>{{ session('internal_storage_path') ? 'アップロード済み' : '未アップロード' }}</p>
                    </div>
                </div>

                <!-- 商品説明 -->
                <div class="form-group-wrap">
                    <div class="form-group-textarea">
                        <label for="product-description">商品説明</label>
                        <p>{{ session('product_description', '説明がありません') }}</p>
                    </div>
                </div>

                <!-- 公開設定スイッチ -->
                <div class="form-group-wrap switch-group">
                    <div class="form-group">
                        <label for="public-setting">公開設定</label>
                        <p>{{ session('is_published') ? '公開' : '非公開' }}</p>
                    </div>
                </div>

                <!-- 確認ボタン -->
                <div class="form-group-wrap group-btn">
                    <div class="form-group-btn">
                        <button type="submit" class="submit-btn">登録</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</article>


@endsection

<!-- JavaScript -->
@push('scripts')
<script src="{{ asset('js/scripts.js') }}"></script>
<script src="{{ asset('/js/input_password.js') }}"></script>
@endpush