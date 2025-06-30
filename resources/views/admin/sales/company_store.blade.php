@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/sales/company_store.css') }}">
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
    <h2 class="section-title">提携先企業登録</h2>

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
            <form action="{{ route('companies.store.confirm') }}" method="post">
                @csrf
                <!-- 機種画像 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="product-image">提携先企業名</label>
                        <input type="text" id="company_name" name="company_name" class="form-input">
                    </div>
                </div>

                <!-- 商品名とメーカー名 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="product-name">初回報酬</label>
                        <input type="text" id="initial_reward_percentage" name="initial_reward_percentage" class="form-input" placeholder="数字のみ">
                    </div>
                </div>

                <!-- カテゴリーと金額 -->
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="category">初回適応回数</label>
                        <input type="text" id="initial_reward_times" name="initial_reward_times" class="form-input" placeholder="数字のみ">
                    </div>
                </div>
                <div class="form-group-wrap">
                    <div class="form-group">
                        <label for="category">継続報酬</label>
                        <input type="text" id="recurring_reward_percentage" name="recurring_reward_percentage" class="form-input" placeholder="数字のみ">
                    </div>
                </div>

                <!-- 注釈 -->
                <p class="important-text">※企業コード、登録日、URLは登録後に自動的に生成されます。


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
<script src="{{ asset('/js/scripts.js') }}"></script>
<script src="{{ asset('/js/input_password.js') }}"></script>
@endpush