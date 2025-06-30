@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/sales/sales_detail.css') }}">
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
    <h2 class="section-title">注文情報</h2>

    <section class="sales-info-section-wrap">
        <div class="sales-info-section">
            <form action="" method="post">

                <!-- ユーザー情報 -->
                <div class="sales-info-group-wrap">
                    <div class="sales-info-group">
                        <label for="sales-name">購入番号</label>
                        <p>{{ $order->order_number }}</p>
                    </div>
                    <div class="sales-info-group">
                        <label for="sales-email">購入者名</label>
                        <p>{{ $order->user_name }}</p>
                    </div>
                </div>

                <!-- 誕生日と登録日 -->
                <div class="sales-info-group-wrap">
                    <div class="sales-info-group">
                        <label for="birth-date">企業コード</label>
                        <p>
                            @if ($order->referral_code)
                            {{ $order->referral_code }}
                            @else
                            提携企業はありません
                            @endif
                        </p>
                    </div>
                    <div class="sales-info-group">
                        <label for="registration-date">購入日</label>
                        <p>{{ $order->order_date }}</p>
                    </div>
                </div>

                <!-- 更新日とステータス -->
                <div class="sales-info-group-wrap">
                    <div class="sales-info-group">
                        <label for="update-date">金額</label>
                        <p>{{ '¥' . number_format($order->total_price) }}</p>
                    </div>
                </div>

                <!-- 購入情報とボタン -->
                <div class="sales-info-group-wrap group-btn">
                    <div class="purchase-info-container">
                        <table class="purchase-info-table">
                            <thead>
                                <tr>
                                    <th>購入番号</th>
                                    <th>商品名</th>
                                    <th>購入日</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchasedItems as $item)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $order->order_date }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3">購入情報が存在しません</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @php
                    $previous = session('previous_page');
                    @endphp

                    <a href="{{
    $previous === 'monthly'
        ? route('show.monthly.sales')
        : ($previous === 'daily'
            ? route('show.daily.sales')
            : ($previous === 'company'
                ? route('show.company.detailsale', ['referralCode' => $order->referral_code])
                : '#'
            )
        )
}}" class="back-btn">戻る</a>


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