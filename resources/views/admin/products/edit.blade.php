@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/products/edit.css') }}">
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
    <h2 class="section-title">商品編集</h2>
    <section class="form-section-wrap">
        <div class="form-section">
            <form action="{{ route('product.edit.temporary', ['id' => $product->id]) }}" method="post">
                @csrf

                <!-- 機種画像 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="product-image">機種画像</label>
                        <input type="text" id="product-image" name="product_image" class="form-input" value="{{ old('product_image', $product->image_path) }}">
                        @error('product_image')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- 商品名とメーカー名 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="product-name">商品名</label>
                        <input type="text" id="product-name" name="product_name" class="form-input" value="{{ old('product_name', $product->name) }}">
                        @error('product_name')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="maker-name">メーカー名</label>
                        <input type="text" id="maker-name" name="maker_name" class="form-input" value="{{ old('maker_name', $product->manufacturer) }}">
                        @error('maker_name')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- カテゴリーと金額 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="category">カテゴリー</label>
                        <input type="text" id="category" name="category" class="form-input" value="{{ old('category', $product->category) }}">
                        @error('category')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="price">金額</label>
                        <input type="text" id="price" name="price" class="form-input" value="{{ old('price', $product->price) }}">
                        @error('price')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- 発売日と内部計算データ保存先 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="release-date">発売日</label>
                        <input type="text" id="release-date" name="release_date" class="form-input" value="{{ old('release_date', $product->release_date) }}">
                        @error('release_date')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="internal-storage">内部計算データ保存先</label>
                        <input type="password" id="internal-storage" name="internal_storage" class="form-input" value="{{ old('internal_storage', $product->python_file_path) }}">
                        @error('internal_storage')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- 商品説明 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="product-description">商品説明</label>
                        <textarea id="product-description" name="product_description" class="form-input product-introduce">{{ old('product_description', $product->description) }}</textarea>
                        @error('product_description')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- 公開設定スイッチ -->
                <div class="form-group-wrap switch-group">
                    <div class="toggle_button">
                        <label for="public-setting">公開設定</label>
                        <input id="toggle" class="toggle_input" type="checkbox" name="is_published" value="1" {{ old('is_published', $product->is_published) ? 'checked' : '' }}>
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

<!-- JavaScript -->
@push('scripts')
<script src="{{ asset('js/scripts.js') }}"></script>
<script src="{{ asset('/js/input_password.js') }}"></script>
@endpush