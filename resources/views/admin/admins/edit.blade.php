@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/users/edit.css') }}">
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
    <h2 class="section-title">ユーザー情報変更</h2>

    @if (session('success'))
    <div id="message-box" class="message-box success">
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
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <section class="user-form-section-wrap">
        <div class="user-form-section">
            <form action="{{ route('admin.update', ['id' => $user->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <!-- ユーザー名 -->
                <div class="user-info-group-wrap">
                    <div class="user-info-group">
                        <label for="user-name">ユーザー名</label>
                        <input type="text" id="user-name" name="user_name" class="user-input" value="{{ old('user_name', $user->name) }}">
                    </div>
                </div>

                <!-- メールアドレス -->
                <div class="user-info-group-wrap">
                    <div class="user-info-group">
                        <label for="user-email">Eメール</label>
                        <input type="text" id="user-email" name="user_email" class="user-input" value="{{ old('user_email', $user->email) }}">
                    </div>
                </div>

                <!-- 権限 -->
                <div class="user-info-group-wrap">
                    <div class="user-info-group">
                        <label for="authority">権限</label>
                        <div class="admin-controls">
                            <!-- 既存の authority フィールドの値を選択済みにする -->
                            <select name="authority" class="admin-filter">
                                <option value="administrator" {{ $admin->authority === 'administrator' ? 'selected' : '' }}>Administrator</option>
                                <option value="editor" {{ $admin->authority === 'editor' ? 'selected' : '' }}>Editor</option>
                                <option value="viewer" {{ $admin->authority === 'viewer' ? 'selected' : '' }}>Viewer</option>
                            </select>
                        </div>
                    </div>
                </div>


                <!-- 確認ボタン -->
                <div class="user-btn-group">
                    <div class="user-info-group">
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
<script src="{{ asset('js/input_password.js') }}"></script>
@endpush