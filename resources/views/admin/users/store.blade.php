@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/users/store.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/global/admin_dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/global/error_message.css') }}">
<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
@endpush

@section('title','管理者ダッシュボード')

@section('breadcrumbs')
<a href="{{  route('admin.dashboard') }}">ホーム</a>　>　登録者一覧
@endsection

@section('content')

@include('admin.global.dashboard.header',['admin' => $admin])

<article id="main-content">
    <h2 class="section-title">商品登録</h2>
    <section class="form-section-wrap">
        <div class="form-section">
            <form action="{{ route('admin.user.store') }}" method="post">
                <!-- 機種画像 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="name">ユーザー名</label>
                        <input type="text" id="product-image" name="name" class="form-input">
                    </div>
                </div>

                <!-- 商品名とメーカー名 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="email">メールアドレス</label>
                        <input type="text" id="product-name" name="email" class="form-input">
                    </div>
                </div>

                <!-- カテゴリーと金額 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="date_of_birth">生年月日</label>
                        <input type="text" id="category" name="date_of_birth" class="form-input">
                    </div>
                </div>

                <!-- パスワード入力 -->
                <div class="password-container">
                    <div class="password-header">
                        <label for="password">パスワード</label>
                    </div>
                    <div class="password-input-wrapper">
                        <input type="password" name="password" id="password" class="password-input">
                        <span class="toggle-visibility">
                            <img src="../img/see.svg" alt="パスワード表示切替" id="toggle-password-visibility">
                        </span>
                    </div>
                </div>

                <!-- パスワード確認用入力 -->
                <div class="password-container">
                    <div class="password-header">
                        <label for="password">パスワード（確認用）</label>
                    </div>
                    <div class="password-input-wrapper">
                        <input type="password" name="password-confirm" id="password-confirm" class="password-input">
                        <span class="toggle-visibility">
                            <img src="../img/see.svg" alt="パスワード表示切替" id="toggle-password-confirm-visibility">
                        </span>
                    </div>
                </div>


                <!-- 確認ボタン -->
                <div class="form-group-wrap group-btn">
                    <div class="form-group">
                        <button type="submit" class="submit-btn">仮登録</button>
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
<script src="{{ asset('js/input_password.js') }}"></script>
@endpush