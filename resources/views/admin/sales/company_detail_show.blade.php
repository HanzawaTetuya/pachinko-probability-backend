@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/sales/company_detail_show.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/global/admin_dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/global/error_message.css') }}">
@endpush

@section('title','管理者ダッシュボード')

@section('breadcrumbs')
<a href="{{  route('admin.dashboard') }}">ホーム</a>　>　ログイン
@endsection

@section('content')

@include('admin.global.dashboard.header',['admin' => $admin])

@php
$now = \Carbon\Carbon::now();
$formattedMonth = $now->format('Y/m');
@endphp

<article id="main-content">
    <h2 class="section-title">{{ $company->company_name }}<span>({{ $company->referral_code }})</span>の{{ $formattedMonth }}の売り上げ</h2>

    <section class="profit-content-wrap">
        <div class="profit-wrap profit-number-wrap">
            <p class="profit-title">
                販売件数
            </p>
            <div class="profit">
                <div class="number-wrap">
                    <p class="number">{{ $company->transactions }}</p>
                    <p class="tanni">件</p>
                </div>
            </div>
        </div>
        <div class="profit-wrap">
            <p class="profit-title">
                合計売上
            </p>
            <div class="profit">
                <div class="amount-wrap">
                    <p class="amount">{{ number_format($company->sales) }}</p>
                    <p class="tanni">円</p>
                </div>
            </div>
        </div>
        <div class="profit-wrap">
            <p class="profit-title">
                報酬金額
            </p>
            <div class="profit">
                <div class="amount-wrap">
                    <p class="amount">{{ number_format($company->rewards) }}</p>
                    <p class="tanni">円</p>
                </div>
            </div>
        </div>
    </section>

    <section class="partner-companies-wrap">
        <div class="p-c-title">
            <p>月間売上</p>
            <div class="search-bar">
                <select id="search-option">
                    <option selected>すべて</option>
                    <option value="option1">購入番号</option>
                    <option value="option2">購入者</option>
                    <option value="option3">企業コード</option>
                    <option value="option3">購入日</option>
                    <option value="option3">金額</option>
                </select>
                <input type="text" placeholder="日別売上 20件" id="search-input">
                <button type="submit">
                    <img src="../img/serch_icon.svg" alt="">
                </button>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>購入番号</th>
                    <th>購入者</th>
                    <th>企業コード</th>
                    <th>購入日</th>
                    <th>金額</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td class="detail-link">
                        <form action="{{ route('show.sales.detail') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_number" value="{{ $order->order_number }}">
                            <input type="hidden" name="previous" value="company">
                            <button type="submit" class="link-button">{{ $order->order_number }}</button>
                        </form>
                    </td>
                    <td>{{ $order->user_id }}</td>
                    <td>{{ $order->referral_code }}</td>
                    <td>{{ $order->created_at->format('Y年n月j日（H:i:s）') }}</td>
                    <td>{{ number_format($order->total_price) }}円</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </section>

    <div class="page-nation">

    </div>

    <section class="company-info-section-wrap">
        <div class="company-info-section">
            <p class="company-info-title">企業情報</p>
            <form action="{{ route('edit.company') }}" method="post">
                @csrf

                <div class="company-info-group-wrap">
                    <div class="company-info-group">
                        <label for="company-name">企業名</label>
                        <p>{{ $company->company_name }}</p>
                    </div>
                    <div class="company-info-group company-info-long">
                        <label for="company-email">報酬形態</label>
                        <p>{{ $company->reward }}</p>
                    </div>
                </div>

                <div class="company-info-group-wrap">
                    <div class="company-info-group">
                        <label for="birth-date">企業コード</label>
                        <p>{{ $company->referral_code }}</p>
                        <input type="hidden" name="referral_code" value="{{ $company->referral_code }}">
                    </div>
                    <div class="company-info-group">
                        <label for="registration-date">登録日</label>
                        <p>{{ $company->created_at }}</p>
                    </div>
                    <div class="company-info-group">
                        <label for="update-date">URL</label>
                        <p>{{ $company->account_create_url }}</p>
                    </div>
                </div>


                <!-- 購入情報とボタン -->
                <div class="company-info-btn">
                    <button class="back-btn">報酬形態の変更</button>
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