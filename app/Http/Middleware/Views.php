<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\admin\DashboardController;
use App\Models\Post;
use App\Models\Product;
use App\Models\Views as ModelsViews;
use App\Models\ViewsPost;
use App\Models\ViewsProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Views
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->createViews($request);

        if (substr($request->getRequestUri(), 0, 8) == '/tin-tuc' && (Str::contains($request->getRequestUri(), '/chi-tiet'))) {
            $this->createViewsPost($request);
        };
        if (substr($request->getRequestUri(), 0, 9) == '/san-pham' && (Str::contains($request->getRequestUri(), '/chi-tiet'))) {
            $this->createViewsProduct($request);
        };
        return $next($request);
    }

    private function createViews(Request $request) {
        $item = ModelsViews::where('ip_address', $request->ip())->first();
        if ($item) {
            if ($item->updated_at < Carbon::now()->subDay()) {
                $times = $item->times + 1;
                $data = [
                    'times' => $times,
                ];
    
                try {
                    DB::beginTransaction();
                    $item->update($data);
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    Log::error($th);
                }
            }
        } else {
            $data = [
                'ip_address' => $request->ip(),
            ];

            try {
                DB::beginTransaction();
                ModelsViews::create($data);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                Log::error($th);
            }
        }
    }

    private function createViewsPost(Request $request) {
        $postSlug = substr($request->getRequestUri(), 9, -9);
        $postData = Post::where('slug', $postSlug)->first();
        $item = ViewsPost::where('ip_address', $request->ip())
                    ->where('post_id', $postData->id)
                ->first();
        if ($item) { 
            if ($item->updated_at < Carbon::now()->subDay()) {
                // $times = $item->times + 1;
                // $data = [
                //     'times' => $times,
                // ];
                try {
                    DB::beginTransaction();
                    $item->increment('times');
                    // $item->update($data);
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    Log::error($th);
                }
            }
        } else {
            $data = [
                'ip_address' => $request->ip(),
                'post_id' => $postData->id
            ];

            try {
                DB::beginTransaction();
                ViewsPost::create($data);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                Log::error($th);
            }
        }
    }

    private function createViewsProduct(Request $request) {
        $productSlug = basename($request->getRequestUri());
        $productData = Product::where('slug', $productSlug)->first();
        $item = ViewsProduct::where('ip_address', $request->ip())
                    ->where('product_id', $productData->id)
                ->first();
        if ($item) { 
            if ($item->updated_at < Carbon::now()->subDay()) {
                $times = $item->times + 1;
                $data = [
                    'times' => $times,
                ];
    
                try {
                    DB::beginTransaction();
                    $item->update($data);
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    Log::error($th);
                }
            }
        } else {
            $data = [
                'ip_address' => $request->ip(),
                'product_id' => $productData->id
            ];

            try {
                DB::beginTransaction();
                ViewsProduct::create($data);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                Log::error($th);
            }
        }
    }
}
