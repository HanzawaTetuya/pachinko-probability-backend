@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/products/store.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/validation_message.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/global/admin_dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/global/error_message.css') }}">
@endpush

@section('title','管理者ダッシュボード')

@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">ホーム</a> > ログイン
@endsection

@section('content')

@include('admin.global.dashboard.header',['admin' => $admin])

<article id="main-content">
    <h2 class="section-title">商品登録</h2>
    <section class="form-section-wrap">
        <div class="form-section">
            <form action="{{ route('products.store.temporary') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- 機種画像 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="product-image">機種画像</label>
                        <input type="file" id="product-image" name="product_image" class="form-input">
                        @error('product_image')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- 商品名とメーカー名 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="product-name">商品名</label>
                        <input type="text" id="product-name" name="product_name" class="form-input" value="{{ old('product_name') }}">
                        @error('product_name')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="maker-name">メーカー名</label>
                        <input type="text" id="maker-name" name="maker_name" class="form-input" value="{{ old('maker_name') }}">
                        @error('maker_name')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- カテゴリーと金額 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="category">カテゴリー</label>
                        <input type="text" id="category" name="category" class="form-input" value="{{ old('category') }}">
                        @error('category')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="price">金額</label>
                        <input type="number" step="0.01" id="price" name="price" class="form-input" value="{{ old('price') }}">
                        @error('price')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- 発売日とPythonファイルパス -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="release-date">発売日</label>
                        <input type="date" id="release-date" name="release_date" class="form-input" value="{{ old('release_date') }}">
                        @error('release_date')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="python-file">内部計算データ保存先 (Pythonファイル)</label>
                        <input type="file" id="python-file" name="internal_storage" class="form-input">
                        @error('internal_storage')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- 商品説明 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="product-description">商品説明</label>
                        <textarea id="product-description" name="product_description" class="form-input product-introduce">{{ old('product_description') }}</textarea>
                        @error('product_description')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- 公開設定スイッチ -->
                <div class="form-group-wrap switch-group">
                    <div class="toggle_button">
                        <label for="public-setting">公開設定</label>
                        <input type="hidden" name="is_published" value="0"> <!-- デフォルト値 0 を設定 -->
                        <input id="toggle" class="toggle_input" type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                        <label for="toggle" class="toggle_label"></label>
                        @error('is_published')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <!-- 確認ボタン -->
                <div class="form-group-wrap group-btn">
                    <div class="form-group">
                        <button type="submit" class="submit-btn">確認</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</article>


@endsection

@push('scripts')
<script src="{{ asset('js/scripts.js') }}"></script>
<script src="{{ asset('js/input_password.js') }}"></script>
@endpush