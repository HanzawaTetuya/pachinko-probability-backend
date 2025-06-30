@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/admins&users/show.css') }}">
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
    <h2 class="section-title">管理者詳細</h2>

    @if (session('success'))
    <div id="message-box" class="message-box success">
        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="8.5" cy="8.5" r="8.5" fill="currentColor" />
            <g clip-path="url(#clip0_61_223)">
                <path d="M13.0848 3.7627C10.1777 5.37772 7.23487 9.97185 7.23487 9.97185L4.57929 6.95708L3 8.46442L6.83995 13.2376L8.23965 13.1839C10.0343 7.80008 14 4.19339 14 4.19339L13.0848 3.7627Z" fill="white" />
            </g>
            <defs>
                <clipPath id="clip0_61_223">
                    <rect width="11" height="11" fill="white" transform="translate(3 3)" />
                </clipPath>
            </defs>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <section class="user-info-section-wrap">
        <div class="user-info-section">
            <form action="" method="post">

                <!-- ユーザー情報 -->
                <div class="user-info-group-wrap">
                    <div class="user-info-group">
                        <label for="user-name">管理者名</label>
                        <p>{{ $user->name }}</p>
                    </div>
                    <div class="user-info-group">
                        <label for="user-email">メールアドレス</label>
                        <p>{{ $user->email }}</p>
                    </div>
                </div>

                <!-- 誕生日と登録日 -->
                <div class="user-info-group-wrap">
                    <div class="user-info-group">
                        <label for="birth-date">生年月日</label>
                        <p>{{ $user->birthday }}</p>
                    </div>
                    <div class="user-info-group">
                        <label for="registration-date">登録日</label>
                        <p>{{ $user->created_at }}</p>
                    </div>
                </div>

                <!-- 更新日と権限 -->
                <div class="user-info-group-wrap">
                    <div class="user-info-group">
                        <label for="update-date">更新日</label>
                        <p>{{ $user->updated_at }}</p>
                    </div>
                    <div class="user-info-group">
                        <label for="status">権限</label>
                        <p>{{ $user->authority }}</p>
                    </div>
                </div>


                <!-- ボタン -->
                <div class="user-info-group-wrap group-btn">

                    <div class="user-info-btn">

                        <!-- 編集ボタン -->
                        <a href="{{ route('admin.edit.button', ['id' => $user->id]) }}" class="edit-user-btn">Edit</a>

                        <!-- 戻るボタン -->
                        <a href="{{ route('admins.show') }}" class="back-btn">戻る</a>


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