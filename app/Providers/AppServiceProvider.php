<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Social;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

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

        // DB::statement("SET GLOBAL max_allowed_packet = 67108864");
        $categories = Category::where([['parent_id', null], ['status', 1]])->orderBy('id', 'ASC')->get();
        $socials = Social::orderBy('id', 'DESC')->get(); 
        View::share(['categories' => $categories, 'socials' => $socials]);
    }
}
