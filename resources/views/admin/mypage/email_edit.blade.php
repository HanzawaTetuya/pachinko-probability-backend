@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/mypage/email_edit.css') }}">
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
    <h2 class="title">Eメールを変更</h2>

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
        <div class="user-info-section">
            <div class="container-header">
                <p>現在のEメール:</p>
                <p>{{ $admin -> email }}</p>
                <p>アカウントに関連付けられているEメールを変更する場合は、下で変更することができます。完了したら［続行］ボタンをクリックしてください。</p>
                <p>新しいEメールアドレスに認証コードが送信されます。</p>
            </div>
            <form action="{{ route('admin.email.verify') }}" method="POST">
                @csrf
                <div class="email-container">
                    <div class="email-header">
                        <label for="email">新しいEメール</label>
                    </div>
                    <div class="email-input-wrapper">
                        <input type="email" name="email" id="email" class="email-input">
                    </div>
                </div>
                <button name="login" class="login-button">保存</button>
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