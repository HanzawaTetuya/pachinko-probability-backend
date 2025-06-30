@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/mypage/username_edit.css') }}">
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
    <h2 class="title">名前を変更</h2>

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
                <p>アカウントに関連付けられている名前を変更する場合は、下で変更することができます。完了したら［保存］ボタンをクリックしてください。</p>
            </div>
            <div class="name-container">
                <div class="name-header">
                    <label for="name">新しいユーザー名</label>
                </div>
                <form action="{{ route('admin.mypage.update', ['type' => 'name']) }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="name-input-wrapper">
                        <!-- フォームをPOSTメソッドに変更し、CSRFトークンを含める -->
                        <input type="text" name="name" id="name" class="name-input" value="{{ old('name', $admin->name) }}" required>
                    </div>

                    <button type="submit" class="login-button">保存</button>
                </form>
            </div>
        </div>
    </section>

</article>

@endsection

<!-- JavaScript -->
@push('scripts')
<script src="{{ asset('/js/scripts.js') }}"></script>
<script src="{{ asset('/js/input_password.js') }}"></script>
@endpush