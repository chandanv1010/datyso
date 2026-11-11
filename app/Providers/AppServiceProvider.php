<?php

namespace App\Providers;

use App\Mail\Transports\PhpMailTransport;
use App\Models\Category;
use App\Models\Social;
use Illuminate\Mail\MailManager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();

        try {
            $mailManager = $this->app->make('mail.manager');
            if ($mailManager instanceof MailManager) {
                $mailManager->extend('php_mail', static function () {
                    return new PhpMailTransport();
                });
            }
        } catch (\Throwable $e) {
            // An toàn khi mail.manager chưa sẵn sàng
        }

        $categories = collect();
        $socials = collect();

        try {
            if (Schema::hasTable('m_category') && Schema::hasTable('m_social')) {
                $categories = Category::where([['parent_id', null], ['status', 1]])->orderBy('id', 'ASC')->get();
                $socials = Social::orderBy('id', 'DESC')->get();
            }
        } catch (\Throwable $e) {
            // Tránh lỗi khi database chưa chạy migrate hoặc đang khởi tạo
        }

        View::share(['categories' => $categories, 'socials' => $socials]);
    }
}
