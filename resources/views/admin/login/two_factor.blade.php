@extends('admin.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/login/two_factor.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/global/public.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/global/error_message.css') }}">
@endpush

@section('title','二段階認証')

@section('content')
<main id="main-content-wrapper">
    <article id="main-content">
        <h2 class="section-title">2段階認証</h2>


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
                <div class="text-wrap">
                    <p>登録されたメールアドレス宛に認証コードを送信しました。</p>
                    <p>有効期限は10分です。お早めにログインの処理をしてください。</p>
                </div>

                <form action="{{ route('verify.two_factor') }}" method="post">
                    @csrf
                    <div class="password-container">
                        <div class="password-header">
                            <label for="password">コード</label>
                        </div>
                        <div class="password-input-wrapper">
                            <input type="password" name="auth-code" id="auth-code" class="password-input">
                            <span class="toggle-visibility">
                                <img src="{{ asset('img/see.svg') }}" alt="パスワード表示切替" id="toggle-password-visibility">
                            </span>
                        </div>
                    </div>

                    <div class="form-group-wrap group-btn">
                        <div class="form-group">
                            <button type="submit" class="submit-btn" onclick="location.href='admin_admin_two_factor.html'">認証</button>
                        </div>
                    </div>
                </form>

            </div>
        </section>
    </article>
</main>
@endsection

<!-- JavaScript -->
@push('scripts')
<script src="{{ asset('js/scripts.js') }}"></script>
<script src="{{ asset('js/input_password.js') }}"></script>
@endpush