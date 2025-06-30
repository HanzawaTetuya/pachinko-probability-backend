@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/admins&users/two_factor.css') }}">
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
    <h2 class="section-title">２段階認証</h2>

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

    <section class="form-section-wrap">
        <div class="form-section">
            <div class="form-header">
                <p>管理者宛に認証コードを送信しました。</p>
                <p>管理者からの連絡にコードが含まれますので、お待ちください。</p>
                <p>また、閲覧理由に【緊急】と記載した場合、1分以内に連絡が来ない場合は直轄管理者に連絡してください。電話番号は下記になります。</p>
                <p>緊急連絡先：080-6352-4605</p>
            </div>

            <form action="{{ route('users.verify.two.factor') }}" method="post">
                @csrf

                <div class="input-container">
                    <div class="input-header">
                        <label for="auth-code">認証コード</label>
                    </div>
                    <div class="input-wrapper">
                        <input type="password" name="auth-code" id="auth-code" class="input-field">
                        <span class="toggle-visibility">
                            <img src="{{ asset('img/see.svg') }}" data-no-see-img="{{ asset('img/no-see.svg') }}" alt="パスワード表示切替" id="toggle-password-visibility">
                        </span>
                    </div>
                </div>
                <button class="submit-button">認証</button>

            </form>

        </div>
    </section>
</article>

@endsection

<!-- JavaScript -->
@push('scripts')
<script src="{{ asset('/js/scripts.js') }}"></script>
<script src="{{ asset('/js/input_password.js') }}"></script>
@endpush