@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/products/show.css') }}">
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
    <h2 class="section-title">商品一覧

        <!-- error message area laravelでメッセージのやつを入れてね。 -->

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

        <!-- 登録画面構築完了後に二段階認証画面に変更 -->
        <form action="{{ route('products.store.show') }}" method="get">
            <button class="add-product-btn">+ Add Product</button>
        </form>


    </h2>


    <!-- 単品商品セクション -->
    <section class="product-section">

        <!-- 検索ボックス -->
        <div class="product-header">
            <h3 class="product-title">単品商品</h3>
            <div class="product-controls">
                <form action="{{ route('products.show') }}" method="GET">
                    <select name="filter" class="product-filter">
                        <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>すべて</option>
                        <option value="name" {{ request('filter') == 'name' ? 'selected' : '' }}>商品名</option>
                        <option value="product_number" {{ request('filter') == 'product_number' ? 'selected' : '' }}>商品コード</option>
                        <option value="price" {{ request('filter') == 'price' ? 'selected' : '' }}>金額</option>
                        <option value="manufacturer" {{ request('filter') == 'manufacturer' ? 'selected' : '' }}>メーカー</option>
                        <option value="category" {{ request('filter') == 'category' ? 'selected' : '' }}>ジャンル</option>
                        <option value="is_published" {{ request('filter') == 'is_published' ? 'selected' : '' }}>公開/非公開</option>
                    </select>
                    <!-- 直接商品名やコードを検索できるテキスト入力フィールド -->
                    <input type="text" name="search" class="product-search-input" value="{{ request('search') }}" placeholder="検索キーワードを入力">
                    <button type="submit" class="search-btn">
                        <svg width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="7.5" cy="7.5" r="6.75" stroke="black" stroke-width="1.5" />
                            <path
                                d="M17.2525 19.6643C17.6194 20.0771 18.2515 20.1144 18.6643 19.7475C19.0771 19.3806 19.1144 18.7485 18.7475 18.3357L17.2525 19.6643ZM11.606 13.3108L17.2525 19.6643L18.7475 18.3357L13.101 11.9822L11.606 13.3108Z"
                                fill="black" />
                        </svg>

                    </button>
                </form>
            </div>
        </div>


        <div class="product-list single-product-list">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>商品名</th>
                        <th>商品コード</th>
                        <th>金額</th>
                        <th>メーカー</th>
                        <th>ジャンル</th>
                        <th>公開/非公開</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr>
                        <td>
                            <a href="{{ route('product.show', ['id' => $product->id]) }}">{{ $product->name }}</a>
                        </td>
                        <td>{{ $product->product_number }}</td>
                        <td>{{ number_format($product->price, 0) }}円</td>
                        <td>{{ $product->manufacturer }}</td>
                        <td>{{ $product->category }}</td>
                        <td>{{ $product->is_published ? '公開中' : '非公開' }}</td>
                        <td>
                            <form action="{{ route('product.edit.show', ['id' => $product->id]) }}">
                                <button class="edit-btn">+ Edit</button>
                            </form>
                            <button class="delete-btn">
                                <svg width="20" height="26" viewBox="0 0 20 26" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M1.65452 23.1015C1.77203 24.4314 2.88601 25.4512 4.22149 25.4512H15.7303C17.0653 25.4512 18.1797 24.4314 18.2973 23.1015L19.5719 6.40283H0.379883L1.65452 23.1015ZM18.1705 7.70158L17.0036 22.9869C16.9449 23.6515 16.3974 24.1525 15.7303 24.1525H4.22149C3.5544 24.1525 3.0069 23.6515 2.94939 23.0026L1.78133 7.70158H18.1705Z"
                                        fill="#EE4C4C" />
                                    <path
                                        d="M6.34388 21.6405C6.63602 21.6231 6.85837 21.3721 6.84057 21.0803L6.33667 11.0275C6.31888 10.7354 6.06819 10.513 5.77606 10.5308C5.48392 10.5485 5.26157 10.7997 5.27931 11.0913L5.78321 21.1442C5.80101 21.4363 6.05214 21.6582 6.34388 21.6405Z"
                                        fill="#EE4C4C" />
                                    <path
                                        d="M9.97506 21.6416C10.2676 21.6416 10.5048 21.4049 10.5048 21.1123V11.0591C10.5048 10.7665 10.2676 10.5298 9.97506 10.5298C9.68252 10.5298 9.44531 10.7665 9.44531 11.0591V21.1123C9.44536 21.4049 9.68252 21.6416 9.97506 21.6416Z"
                                        fill="#EE4C4C" />
                                    <path
                                        d="M13.6051 21.6405C13.8973 21.6582 14.148 21.4363 14.1657 21.1442L14.6692 11.0913C14.6865 10.7997 14.4646 10.5485 14.1725 10.5308C13.8803 10.513 13.6297 10.7354 13.6119 11.0275L13.1084 21.0803C13.0907 21.372 13.313 21.6231 13.6051 21.6405Z"
                                        fill="#EE4C4C" />
                                    <path
                                        d="M19.0775 3.20707C19.0775 3.20707 17.3835 2.92552 16.9231 2.78559C16.5104 2.66003 12.9068 2.11124 12.9068 2.11124L12.7715 1.15201C12.6786 0.489981 12.1365 0 11.4982 0H9.97455H8.45092C7.81341 0 7.27143 0.489981 7.17758 1.15201L7.04232 2.11124C7.04232 2.11124 3.43996 2.65998 3.02693 2.78559C2.56657 2.92552 0.871696 3.20707 0.871696 3.20707C0.358501 3.34865 0 3.83614 0 4.39328V5.4832H9.9746H19.95V4.39328C19.95 3.83614 19.5915 3.34865 19.0775 3.20707ZM10.985 1.9299H8.96501C8.78576 1.9299 8.64031 1.7845 8.64031 1.6052C8.64031 1.4259 8.78576 1.2805 8.96501 1.2805H10.985C11.1642 1.2805 11.3097 1.4259 11.3097 1.6052C11.3097 1.7845 11.1642 1.9299 10.985 1.9299Z"
                                        fill="#EE4C4C" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <!-- セット商品セクション -->
    <h2 class="section-title">セット商品一覧
        <button class="add-product-btn">+ Create Pack</button>
    </h2>
    <section class="product-section">
        <div class="product-header">
            <h3 class="product-title">セット商品</h3>
            <div class="product-controls">
                <select name="filter" class="product-filter">
                    <option value="all">すべて</option>
                    <!-- 他のフィルターオプションをここに追加 -->
                </select>
                <!-- 直接商品名やコードを検索できるテキスト入力フィールド -->
                <input type="text" class="product-search-input" placeholder="商品名やコードで検索">
                <button class="search-btn">
                    <svg width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="7.5" cy="7.5" r="6.75" stroke="black" stroke-width="1.5" />
                        <path
                            d="M17.2525 19.6643C17.6194 20.0771 18.2515 20.1144 18.6643 19.7475C19.0771 19.3806 19.1144 18.7485 18.7475 18.3357L17.2525 19.6643ZM11.606 13.3108L17.2525 19.6643L18.7475 18.3357L13.101 11.9822L11.606 13.3108Z"
                            fill="black" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="product-list set-product-list">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>セット名</th>
                        <th>商品数</th>
                        <th>金額</th>
                        <th>登録日</th>
                        <th>更新日</th>
                        <th>販売件数</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- セット商品リストのアイテムをループで表示 -->
                    <!-- @for ($i = 0; $i < 3; $i++) -->
                    <tr>
                        <td>セット名 {{$i+1}}</td>
                        <td>{{rand(1, 10)}}</td>
                        <td>{{rand(5000, 15000)}}円</td>
                        <td>2024/08/22</td>
                        <td>2024/08/22</td>
                        <td>20</td>
                        <td>
                            <button class="edit-btn">+ Edit</button>
                            <button class="delete-btn">
                                <svg width="20" height="26" viewBox="0 0 20 26" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M1.65452 23.1015C1.77203 24.4314 2.88601 25.4512 4.22149 25.4512H15.7303C17.0653 25.4512 18.1797 24.4314 18.2973 23.1015L19.5719 6.40283H0.379883L1.65452 23.1015ZM18.1705 7.70158L17.0036 22.9869C16.9449 23.6515 16.3974 24.1525 15.7303 24.1525H4.22149C3.5544 24.1525 3.0069 23.6515 2.94939 23.0026L1.78133 7.70158H18.1705Z"
                                        fill="#EE4C4C" />
                                    <path
                                        d="M6.34388 21.6405C6.63602 21.6231 6.85837 21.3721 6.84057 21.0803L6.33667 11.0275C6.31888 10.7354 6.06819 10.513 5.77606 10.5308C5.48392 10.5485 5.26157 10.7997 5.27931 11.0913L5.78321 21.1442C5.80101 21.4363 6.05214 21.6582 6.34388 21.6405Z"
                                        fill="#EE4C4C" />
                                    <path
                                        d="M9.97506 21.6416C10.2676 21.6416 10.5048 21.4049 10.5048 21.1123V11.0591C10.5048 10.7665 10.2676 10.5298 9.97506 10.5298C9.68252 10.5298 9.44531 10.7665 9.44531 11.0591V21.1123C9.44536 21.4049 9.68252 21.6416 9.97506 21.6416Z"
                                        fill="#EE4C4C" />
                                    <path
                                        d="M13.6051 21.6405C13.8973 21.6582 14.148 21.4363 14.1657 21.1442L14.6692 11.0913C14.6865 10.7997 14.4646 10.5485 14.1725 10.5308C13.8803 10.513 13.6297 10.7354 13.6119 11.0275L13.1084 21.0803C13.0907 21.372 13.313 21.6231 13.6051 21.6405Z"
                                        fill="#EE4C4C" />
                                    <path
                                        d="M19.0775 3.20707C19.0775 3.20707 17.3835 2.92552 16.9231 2.78559C16.5104 2.66003 12.9068 2.11124 12.9068 2.11124L12.7715 1.15201C12.6786 0.489981 12.1365 0 11.4982 0H9.97455H8.45092C7.81341 0 7.27143 0.489981 7.17758 1.15201L7.04232 2.11124C7.04232 2.11124 3.43996 2.65998 3.02693 2.78559C2.56657 2.92552 0.871696 3.20707 0.871696 3.20707C0.358501 3.34865 0 3.83614 0 4.39328V5.4832H9.9746H19.95V4.39328C19.95 3.83614 19.5915 3.34865 19.0775 3.20707ZM10.985 1.9299H8.96501C8.78576 1.9299 8.64031 1.7845 8.64031 1.6052C8.64031 1.4259 8.78576 1.2805 8.96501 1.2805H10.985C11.1642 1.2805 11.3097 1.4259 11.3097 1.6052C11.3097 1.7845 11.1642 1.9299 10.985 1.9299Z"
                                        fill="#EE4C4C" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <!-- @endfor -->

                </tbody>
            </table>
        </div>
    </section>
</article>

@endsection

<!-- JavaScript -->
@push('scripts')
<script src="{{ asset('js/scripts.js') }}"></script>
<script src="{{ asset('/js/input_password.js') }}"></script>
@endpush