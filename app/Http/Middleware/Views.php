<?php

namespace App\Http\Middleware;

use App\Models\Post;
use App\Models\Product;
use App\Models\Views as SiteViews;
use App\Models\ViewsPost;
use App\Models\ViewsProduct;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware ghi nhận lượt truy cập website, bài viết và sản phẩm theo IP mỗi 24 giờ.
 */
class Views
{
    /**
     * Xử lý request đến và ghi nhận lượt xem an toàn mà không làm gián đoạn phản hồi người dùng.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->trackViewsSafely($request);

        return $next($request);
    }

    /**
     * Bao bọc logic đếm lượt xem trong try/catch để lỗi thống kê (nếu có) không gây HTTP 500.
     */
    private function trackViewsSafely(Request $request): void
    {
        $ipAddress = trim((string) $request->ip());
        if ($ipAddress === '') {
            return;
        }

        try {
            $this->recordSiteView($ipAddress);
            $this->recordPostView($request, $ipAddress);
            $this->recordProductView($request, $ipAddress);
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }

    /**
     * Ghi nhận lượt truy cập chung của toàn trang theo địa chỉ IP.
     */
    private function recordSiteView(string $ipAddress): void
    {
        $this->upsertDailyView(SiteViews::class, ['ip_address' => $ipAddress]);
    }

    /**
     * Ghi nhận lượt xem bài viết nếu URL khớp trang chi tiết tin tức.
     */
    private function recordPostView(Request $request, string $ipAddress): void
    {
        $postSlug = $this->resolvePostSlug($request);
        if ($postSlug === null) {
            return;
        }

        $post = Post::where('slug', $postSlug)->first();
        if (! $post) {
            // Bỏ qua an toàn khi slug bài viết không tồn tại để tránh lỗi đọc id trên null.
            return;
        }

        $this->upsertDailyView(ViewsPost::class, [
            'ip_address' => $ipAddress,
            'post_id' => $post->id,
        ]);
    }

    /**
     * Ghi nhận lượt xem sản phẩm nếu URL khớp trang chi tiết sản phẩm.
     */
    private function recordProductView(Request $request, string $ipAddress): void
    {
        $productSlug = $this->resolveProductSlug($request);
        if ($productSlug === null) {
            return;
        }

        $product = Product::where('slug', $productSlug)->first();
        if (! $product) {
            // Bỏ qua an toàn khi slug sản phẩm không tồn tại để tránh lỗi "Attempt to read property id on null".
            return;
        }

        $this->upsertDailyView(ViewsProduct::class, [
            'ip_address' => $ipAddress,
            'product_id' => $product->id,
        ]);
    }

    /**
     * Trích xuất slug bài viết từ route parameter hoặc path thuần, bỏ qua hoàn toàn query string (?utm_..., ?a=1).
     */
    private function resolvePostSlug(Request $request): ?string
    {
        if ($request->routeIs('post.detail')) {
            return $this->normalizeSlug($request->route('postSlug'));
        }

        if (preg_match('#^/tin-tuc/([^/]+)/chi-tiet/?$#', $request->getPathInfo(), $matches)) {
            return $this->normalizeSlug($matches[1]);
        }

        return null;
    }

    /**
     * Trích xuất slug sản phẩm từ route parameter hoặc path thuần, tránh dùng getRequestUri() khiến query string dính vào slug.
     */
    private function resolveProductSlug(Request $request): ?string
    {
        if ($request->routeIs('product.detail')) {
            return $this->normalizeSlug($request->route('productSlug'));
        }

        if (preg_match('#^/san-pham/[^/]+/chi-tiet/([^/]+)/?$#', $request->getPathInfo(), $matches)) {
            return $this->normalizeSlug($matches[1]);
        }

        return null;
    }

    /**
     * Chuẩn hóa slug và cắt bỏ query/fragment phát sinh (nếu có).
     */
    private function normalizeSlug(mixed $slug): ?string
    {
        if (! is_string($slug)) {
            return null;
        }

        $cleanSlug = explode('?', explode('#', trim($slug))[0])[0];
        $cleanSlug = trim($cleanSlug, '/');

        return $cleanSlug !== '' ? $cleanSlug : null;
    }

    /**
     * Tạo mới bản ghi lượt xem hoặc tăng bộ đếm mỗi 24 giờ cho một cặp định danh.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $modelClass
     * @param  array<string, int|string>  $attributes
     */
    private function upsertDailyView(string $modelClass, array $attributes): void
    {
        $item = $modelClass::where($attributes)->first();

        try {
            DB::beginTransaction();

            if (! $item) {
                $modelClass::create($attributes);
            } elseif ($item->updated_at === null || $item->updated_at < Carbon::now()->subDay()) {
                // Chỉ gọi increment('times') nếu bảng hiện tại có cột times (tránh lỗi ở schema tối giản).
                if (array_key_exists('times', $item->getAttributes())) {
                    $item->increment('times');
                } else {
                    $item->touch();
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
        }
    }
}
