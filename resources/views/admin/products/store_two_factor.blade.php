@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/products/two_factor.css') }}">
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
    <section class="form-section-wrap">
        <div class="form-section">
            <div class="form-header">
                <p>管理者宛に認証コードを送信しました。</p>
                <p>管理者からの連絡にコードが含まれますので、お待ちください。</p>
                <p>また、閲覧理由に【緊急】と記載した場合、1分以内に連絡が来ない場合は直轄管理者に連絡してください。電話番号は下記になります。</p>
                <p>緊急連絡先：080-6352-4605</p>
            </div>

            <form action="{{ route('products.store.verify.cod') }}" method="post">
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