<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

class AppServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->mapAdminRoutes();
        $this->mapUserRoutes();

        // 特定のビューでのみログイン情報を渡す
        View::composer('admin.*', function ($view) {
            $admin = Auth::guard('admin')->user();
            $view->with('admin', $admin);
        });
    }

    public function register(): void {}

    protected function mapAdminRoutes()
    {
        Route::prefix('admin')
            ->middleware('web')
            ->group(base_path('routes/admin/auth.php'));

        Route::prefix('admin/dashboard')
            ->middleware('web')
            ->group(base_path('routes/admin/mypage.php'));

        Route::prefix('admin/product')
            ->middleware('web')
            ->group(base_path('routes/admin/product.php'));

        Route::prefix('admin/users')
            ->middleware('web')
            ->group(base_path('routes/admin/users.php'));

        Route::prefix('admin/admins-list')
            ->middleware('web')
            ->group(base_path('routes/admin/admins.php'));


        Route::prefix('admin/sales')
            ->middleware('web')
            ->group(base_path('routes/admin/sales.php'));

    }

    protected function mapUserRoutes()
    {
        Route::prefix('user')
            ->middleware('web')
            ->group(base_path('routes/user/web.php')); // userルートファイルを指定

        Route::prefix('user')
            ->middleware('web')
            ->group(base_path('routes/user/auth.php')); // userルートファイルを指定

        Route::prefix('api/user')
            ->withoutMiddleware(ValidateCsrfToken::class)
            ->middleware('api')
            ->group(base_path('routes/api.php')); // userルートファイルを指定
    }
}
