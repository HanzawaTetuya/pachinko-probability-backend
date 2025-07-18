@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/mypage/two_factor.css') }}">
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
    <h2 class="title">２段階認証</h2>

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

    <form action="{{ route('admin.mypage.edit.two_factor') }}" method="POST">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        <section class="user-info-section-wrap">
            <div class="user-info-section">
                <div class="container-header">
                    <p>登録されたメールアドレス宛に認証コードを送信しました。</p>
                    <p>有効期限は10分なのでお早めに確認し入力してください。</p>
                </div>
                <div class="password-container">
                    <div class="password-header">
                        <label for="password">認証コード</label>
                    </div>
                    <div class="password-input-wrapper">
                        <input type="password" name="auth-code" id="auth-code" class="password-input">
                        <span class="toggle-visibility">
                            <img src="{{ asset('img/see.svg') }}" alt="パスワード表示切替" id="toggle-password-visibility">
                        </span>
                    </div>
                </div>
                <button name="login" class="login-button">認証</button>

            </div>
        </section>

    </form>

</article>

@endsection

<!-- JavaScript -->
@push('scripts')
<script src="{{ asset('/js/scripts.js') }}"></script>
<script src="{{ asset('/js/input_password.js') }}"></script>
@endpush