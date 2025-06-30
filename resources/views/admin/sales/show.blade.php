@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/sales/index.css') }}">
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
    <h2 class="section-title">売上管理</h2>

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

    <section class="profit-content-wrap">
        <div class="profit-wrap">
            <p class="profit-title">
                {{ $today }} の売り上げ
            </p>

            @if ($dailySales)
            <div class="profit">
                <div class="number-wrap">
                    <p class="number">{{ $dailySales->total_orders }}</p>
                    <p class="tanni">
                        <span class="font-size-20">件</span><span class="line">/</span><span class="font-size-20">販売件数</span>
                    </p>
                </div>
                <div class="amount-wrap">
                    <p class="amount">{{ number_format($dailySales->total_sales) }}</p>
                    <p class="tanni">
                        <span class="font-size-20">円</span><span class="line">/</span><span class="font-size-20">売上金額</span>
                    </p>
                </div>
            </div>
            @else
            <div class="profit">
                <p class="text-center">なにしてるんですか？仕事してください。</p>
            </div>
            @endif

            <button class="detail-btn" onclick="location.href='{{ route('show.daily.sales') }}'">詳細</button>
        </div>

        <div class="profit-wrap">
            <p class="profit-title">
                {{ $formattedMonth }} の売り上げ
            </p>

            @if ($monthlySales)
            <div class="profit">
                <div class="number-wrap">
                    <p class="number">{{ $monthlySales->total_orders }}</p>
                    <p class="tanni">
                        <span class="font-size-20">件</span><span class="line">/</span><span class="font-size-20">販売件数</span>
                    </p>
                </div>
                <div class="amount-wrap">
                    <p class="amount">{{ number_format($monthlySales->total_sales) }}</p>
                    <p class="tanni">
                        <span class="font-size-20">円</span><span class="line">/</span><span class="font-size-20">売上金額</span>
                    </p>
                </div>
            </div>
            @else
            <div class="profit">
                <p class="text-center">今月何してたんですか？仕事してください。</p>
            </div>
            @endif

            <button class="detail-btn" onclick="location.href='{{ route('show.monthly.sales') }}'">詳細</button>
        </div>

    </section>

    <section class="partner-companies-wrap">
        <div class="p-c-title">
            <p>提携先一覧</p>
            <button class="companies-add-btn" onclick="location.href='{{ route('companies.store.form') }}'">+ Add</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>企業コード</th>
                    <th>会社名</th>
                    <th>報酬形態</th>
                    <th>当月件数</th>
                    <th>当月売上</th>
                    <th>当月報酬額</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($companies) && $companies->count())
                @foreach ($companies as $company)
                <tr>
                    <td><a href="{{ route('show.company.detailsale', ['referralCode' => $company->referral_code]) }}">{{ $company->referral_code }}</a></td>
                    <td>{{ $company->company_name }}</td>
                    <td>{{ $company->reward }}</td>
                    <td>{{ $company->transactions }}</td>
                    <td>{{ number_format($company->sales) }}円</td>
                    <td>{{ number_format($company->rewards) }}円</td>

                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="6">データが存在しません</td>
                </tr>
                @endif

            </tbody>
        </table>
    </section>

</article>

@endsection

<!-- JavaScript -->
@push('scripts')
<script src="{{ asset('/js/scripts.js') }}"></script>
<script src="{{ asset('/js/input_password.js') }}"></script>
@endpush