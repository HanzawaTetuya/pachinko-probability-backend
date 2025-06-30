@extends('admin.layouts.app_dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/admins&users/index.css') }}">
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
    <h2 class="section-title">登録者一覧

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

        <form action="{{  route('admin.create') }}" method="GET">
            <button class="add-product-btn">+ Add Admin</button>
        </form>

    </h2>


    <!-- user -->
    <section class="product-section">

        <!-- 検索ボックス -->
        <div class="product-header">
            <h3 class="product-title">登録者</h3>
            <div class="product-controls">

                <form action="{{ route('admins.show') }}" method="GET">
                    <select name="filter" class="product-filter">
                        <option value="all">すべて</option>
                        <option value="name" {{ request('filter') == 'name' ? 'selected' : '' }}>ユーザー名</option>
                        <option value="email" {{ request('filter') == 'email' ? 'selected' : '' }}>メールアドレス</option>
                        <option value="authority" {{ request('filter') == 'authority' ? 'selected' : '' }}>権限</option>
                        <option value="created_at" {{ request('filter') == 'created_at' ? 'selected' : '' }}>登録日</option>
                        <option value="updated_at" {{ request('filter') == 'updated_at' ? 'selected' : '' }}>更新日</option>
                        <option value="birthday" {{ request('filter') == 'birthday' ? 'selected' : '' }}>誕生日</option>
                    </select>


                    <!-- 検索ボックス -->
                    <input type="text" name="search" class="product-search-input" value="{{ request('search') }}" placeholder="検索キーワードを入力">
                    <button type="submit" class="search-btn">
                        <svg width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="7.5" cy="7.5" r="6.75" stroke="black" stroke-width="1.5" />
                            <path d="M17.2525 19.6643C17.6194 20.0771 18.2515 20.1144 18.6643 19.7475C19.0771 19.3806 19.1144 18.7485 18.7475 18.3357L17.2525 19.6643ZM11.606 13.3108L17.2525 19.6643L18.7475 18.3357L13.101 11.9822L11.606 13.3108Z" fill="black" />
                        </svg>
                    </button>
                </form>

            </div>
        </div>


        <div class="product-list single-product-list">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>ユーザー名</th>
                        <th>メールアドレス</th>
                        <th>権限</th>
                        <th>登録日</th>
                        <th>更新日</th>
                        <th>誕生日</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($admins as $admin)
                    <tr>
                        <td>
                            <a href="{{ route('admin.show', $admin->id) }}">{{ $admin->name }}</a>
                        </td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->authority }}</td>
                        <td>{{ $admin->created_at }}</td>
                        <td>{{ $admin->updated_at }}</td>
                        <td>{{ $admin->birthday }}</td>
                        <td>
                            <form action="{{ route('admin.edit.button', ['id' => $admin->id]) }}" method="get">

                                <button class="edit-btn">+ Edit</button>

                            </form>

                        </td>
                    </tr>
                    @endforeach

                </tbody>

            </table>

        </div>
    </section>

    <div class="pagination-wrapper">
        {{ $admins->appends(request()->input())->links('pagination.custom') }}
    </div>

</article>

@endsection

<!-- JavaScript -->
@push('scripts')
<script src="{{ asset('js/scripts.js') }}"></script>
<script src="{{ asset('js/input_password.js') }}"></script>
@endpush