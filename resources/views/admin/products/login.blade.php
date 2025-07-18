@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/products/login.css') }}">
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
    <h2 class="title">ログイン</h2>

    @if (session('error'))
    <div id="message-box" class="message-box error">
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
        <span>{{ session('error') }}</span>
    </div>
    @endif

    
    <section class="user-info-section-wrap">

        <form action="{{ route('products.login.request') }}" method="POST">
            @csrf

            <div class="user-info-section">
                <!-- ユーザー情報 -->
                <div class="user-info-item">
                    <label>User name</label>
                    <div class="user-info">
                        <span>{{ $admin -> name }}</span>
                    </div>
                </div>
                <div class="user-info-item">
                    <label>Eメール</label>
                    <div class="user-info">
                        <span>{{ $admin -> email }}</span>
                    </div>
                </div>

                <!-- パスワード入力 -->
                <div class="password-container">
                    <div class="password-header">
                        <label for="password">パスワード</label>
                        <span class="forgot-password-link">
                            <a href="#">パスワードを忘れた方</a>
                        </span>
                    </div>
                    <div class="password-input-wrapper">
                        <input type="password" name="password" id="password" class="password-input">
                        <span class="toggle-visibility">
                            <img src="{{ asset('img/see.svg') }}" data-no-see-img="{{ asset('img/no-see.svg') }}" alt="パスワード表示切替" id="toggle-password-visibility">
                        </span>
                    </div>
                </div>

                <!-- 閲覧理由入力 -->
                <div class="input-container">
                    <div class="input-header">
                        <label for="reason">閲覧理由</label>
                        <p class="input-note">※緊急の場合は、文頭に【緊急】と入れてください。</p>
                    </div>
                    <div class="input-wrapper">
                        <textarea name="reason" id="reason" class="input-field" rows="4"></textarea>
                    </div>
                </div>

                <!-- 送信ボタン -->
                <button name="login" class="login-button">送信</button>
            </div>

        </form>


    </section>
</article>

@endsection

<!-- JavaScript -->
@push('scripts')
<script src="{{ asset('/js/scripts.js') }}"></script>
<script src="{{ asset('/js/input_password.js') }}"></script>
@endpush