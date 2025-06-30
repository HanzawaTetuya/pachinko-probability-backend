@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/sales/days.css') }}">
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
    <h2 class="section-title">{{ $today }} の売り上げ</h2>

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
        <div class="profit-wrap profit-number-wrap">
            <p class="profit-title">販売件数</p>
            @if ($dailySales)
            <div class="profit">
                <div class="number-wrap">
                    <p class="number">{{ $dailySales->total_orders }}</p>
                    <p class="tanni">件</p>
                </div>
            </div>
            @else
            <div class="profit">
                <p class="no-data">ないって言ってるでしょ？</p>
            </div>
            @endif
        </div>

        <div class="profit-wrap">
            <p class="profit-title">合計売上</p>
            @if ($dailySales)
            <div class="profit">
                <div class="amount-wrap">
                    <p class="amount">{{ number_format($dailySales->total_sales) }}</p>
                    <p class="tanni">円</p>
                </div>
            </div>
            @else
            <div class="profit">
                <p class="no-data">頭悪いの？</p>
            </div>
            @endif
        </div>
    </section>


    <section class="partner-companies-wrap">
        <div class="p-c-title">
            <p>日別売上</p>
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
                            <input type="hidden" name="previous" value="daily">
                            <button type="submit" class="link-button">{{ $order->order_number }}</button>
                        </form>
                    </td>
                    <td>{{ $order->user_name }}</td>
                    <td>
                        @if ($order->referral_code)
                        {{ $order->referral_code }}
                        @else
                        提携企業はありません
                        @endif
                    </td>
                    <td>{{ $order->order_date }}</td>
                    <td>{{ '¥' . number_format($order->total_price) }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>

    </section>

    <div class="page-nation">
        {{ $orders->links() }}
    </div>

</article>

@endsection

<!-- JavaScript -->
@push('scripts')
<script src="{{ asset('/js/scripts.js') }}"></script>
<script src="{{ asset('/js/input_password.js') }}"></script>
@endpush