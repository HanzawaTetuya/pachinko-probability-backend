@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/admins&users/store.css') }}">
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
    <h2 class="section-title">管理者登録</h2>
    <section class="form-section-wrap">
        <div class="form-section">
            <form action="{{ route('admin.store') }}" method="POST">
                @csrf

                <!-- ユーザー名 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="user-name">ユーザー名</label>
                        <input type="text" id="user-name" name="name" class="form-input" required>
                    </div>
                </div>

                <!-- メールアドレス -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="user-email">メールアドレス</label>
                        <input type="email" id="user-email" name="email" class="form-input" required>
                    </div>
                </div>

                <!-- 生年月日 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="birthdate">生年月日</label>
                        <input type="date" id="birthdate" name="birthday" class="form-input" required>
                    </div>
                </div>

                <!-- パスワード -->
                <div class="password-container">
                    <div class="password-header">
                        <label for="password">パスワード</label>
                    </div>
                    <div class="password-input-wrapper">
                        <input type="password" name="password" id="password" class="password-input" required>
                        <span class="toggle-visibility">
                            <img src="../img/see.svg" alt="パスワード表示切替" id="toggle-password-visibility">
                        </span>
                    </div>
                </div>

                <!-- パスワード確認用入力 -->
                <div class="password-container">
                    <div class="password-header">
                        <label for="password_confirmation">パスワード（確認用）</label>
                    </div>
                    <div class="password-input-wrapper">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="password-input" required>
                        <span class="toggle-visibility">
                            <img src="../img/see.svg" alt="パスワード表示切替" id="toggle-password-confirm-visibility">
                        </span>
                    </div>
                </div>

                <!-- 確認ボタン -->
                <div class="form-group-wrap group-btn">
                    <div class="form-group">
                        <button type="submit">仮登録</button>
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