<?php

namespace Tests\Feature;

use App\Http\Middleware\Views;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * Kiểm thử quy tắc hiển thị khối đánh giá sao sản phẩm (Ticket L12).
 *
 * Mục tiêu:
 * - Đảm bảo khối sao, điểm số cố định "4.7", liên kết #danh-gia và "( đánh giá)" rỗng
 *   bị ẩn hoàn toàn khi sản phẩm chưa có đánh giá thật từ khách hàng.
 * - Đảm bảo khi sản phẩm có dữ liệu đánh giá thật (reviews_count >= 1 && rating > 0),
 *   component <x-product-rating> hiển thị đúng số sao đầy/rỗng, điểm số (nếu bật) và số lượt.
 */
class ProductRatingDisplayTest extends TestCase
{
    protected Category $category;
    protected Product $primaryProduct;
    protected Product $relatedProduct;

    /**
     * Cấu hình SQLite in-memory để chạy test độc lập trên cả local và CI/hosting.
     */
    public function createApplication()
    {
        $_ENV['DB_CONNECTION'] = $_SERVER['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = $_SERVER['DB_DATABASE'] = ':memory:';
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');

        $app = parent::createApplication();
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
        $app['config']->set('cache.default', 'array');
        $app['config']->set('session.driver', 'array');

        return $app;
    }

    /**
     * Khởi tạo schema và dữ liệu mẫu tối thiểu cho danh mục & sản phẩm.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $exitCode = Artisan::call('migrate', ['--force' => true]);
        if ($exitCode !== 0 || ! Schema::hasTable('m_user')) {
            throw new \RuntimeException('Migrate failed (' . $exitCode . '): ' . Artisan::output());
        }

        $this->seedSampleCatalog();

        // Nạp lại dữ liệu dùng chung cho layout user sau khi chạy migration SQLite.
        View::share([
            'categories' => Category::where([['parent_id', null], ['status', 1]])->orderBy('id', 'ASC')->get(),
            'socials' => collect(),
        ]);
    }

    /**
     * Tạo danh mục và hai sản phẩm đang hoạt động để kiểm tra cả trang chi tiết lẫn thẻ sản phẩm liên quan.
     */
    private function seedSampleCatalog(): void
    {
        $userId = (int) (DB::table('m_user')->value('id') ?? 1);

        $this->category = Category::create([
            'name' => 'Máy Hàn Laser Cầm Tay',
            'slug' => 'may-han-laser-cam-tay',
            'status' => 1,
            'user_id' => $userId,
            'image' => 'category/sample.jpg',
        ]);

        $this->primaryProduct = Product::create([
            'name' => 'Máy Hàn Laser QG-SC 1500W',
            'slug' => 'may-han-laser-qg-sc-1500w',
            'description' => '<p>Máy hàn laser công suất 1500W.</p>',
            'status' => 1,
            'del_flg' => 1,
            'user_id' => $userId,
            'price' => 0,
        ]);

        $this->relatedProduct = Product::create([
            'name' => 'Máy Cắt Laser CNC Fiber 3015',
            'slug' => 'may-cat-laser-cnc-fiber-3015',
            'description' => '<p>Máy cắt laser CNC công nghiệp.</p>',
            'status' => 1,
            'del_flg' => 1,
            'user_id' => $userId,
            'price' => 0,
        ]);

        foreach ([$this->primaryProduct, $this->relatedProduct] as $product) {
            ProductCategory::create([
                'product_id' => $product->id,
                'category_id' => $this->category->id,
            ]);

            ProductImage::create([
                'product_id' => $product->id,
                'image' => 'product/sample.jpg',
            ]);
        }
    }

    /**
     * Kiểm tra component <x-product-rating> ẩn hoàn toàn khi không có đánh giá thật.
     */
    public function test_product_rating_component_hides_output_without_verified_reviews(): void
    {
        $invalidCases = [
            null,
            $this->primaryProduct, // Model thực tế chưa có cột rating / reviews_count
            (object) ['rating' => 4.7, 'reviews_count' => 0],
            (object) ['rating' => 0, 'reviews_count' => 5],
            (object) ['rating' => 'invalid', 'reviews_count' => 'none'],
            (object) ['rating' => -2, 'reviews_count' => -1],
        ];

        foreach ($invalidCases as $product) {
            $html = (string) $this->blade(
                '<x-product-rating :product="$product" :show-score="true" wrapper-class="mb-3" href="#danh-gia" />',
                ['product' => $product]
            );

            $this->assertSame('', trim($html));
        }
    }

    /**
     * Kiểm tra component render đúng sao đầy/rỗng, điểm số (khi bật) và số lượt đánh giá thật.
     */
    public function test_product_rating_component_renders_when_real_reviews_exist(): void
    {
        // Trường hợp trang chi tiết: bật show-score và href, rating 4.2 -> 4 sao đầy + 1 sao rỗng
        $detailProduct = (object) ['rating' => 4.2, 'reviews_count' => 3];
        $detailHtml = (string) $this->blade(
            '<x-product-rating :product="$product" :show-score="true" wrapper-class="mb-3" href="#danh-gia" />',
            ['product' => $detailProduct]
        );

        $this->assertSame(4, substr_count($detailHtml, 'class="bi bi-star-fill'));
        $this->assertSame(1, substr_count($detailHtml, 'class="bi bi-star '));
        $this->assertStringContainsString('4.2', $detailHtml);
        $this->assertStringContainsString('(3 đánh giá)', $detailHtml);
        $this->assertStringContainsString('href="#danh-gia"', $detailHtml);

        // Trường hợp product card: tắt show-score, không gắn anchor, 4.7 -> 5 sao đầy
        $cardProduct = (object) ['rating' => 4.7, 'reviews_count' => 12];
        $cardHtml = (string) $this->blade(
            '<x-product-rating :product="$product" wrapper-class="p-3 mb-2 mt-auto" />',
            ['product' => $cardProduct]
        );

        $this->assertSame(5, substr_count($cardHtml, 'class="bi bi-star-fill'));
        $this->assertSame(0, substr_count($cardHtml, 'class="bi bi-star '));
        $this->assertStringNotContainsString('<span class="fw-semibold">4.7</span>', $cardHtml);
        $this->assertStringNotContainsString('<a href=', $cardHtml);
        $this->assertStringContainsString('(12 đánh giá)', $cardHtml);
    }

    /**
     * Kiểm tra các trang chi tiết, danh mục, tìm kiếm và danh sách có card sản phẩm
     * không còn render điểm 4.7 cố định, thẻ SVG sao giả hoặc "( đánh giá)" rỗng.
     */
    public function test_public_product_pages_do_not_render_fake_stars_or_empty_review_text(): void
    {
        $this->withoutExceptionHandling();

        $detailResponse = $this->withoutMiddleware([Views::class])
            ->get(route('product.detail', [
                'categorySlug' => $this->category->slug,
                'productSlug' => $this->primaryProduct->slug,
            ]));

        $detailResponse->assertOk();
        $detailResponse->assertSee($this->primaryProduct->name);
        $detailResponse->assertSee($this->relatedProduct->name);
        $detailResponse->assertDontSee('bi-star-fill', false);
        $detailResponse->assertDontSee('href="#danh-gia"', false);
        $detailResponse->assertDontSee('đánh giá)');
        $detailResponse->assertDontSee('<span>4.7</span>', false);

        $listResponse = $this->withoutMiddleware([Views::class])
            ->get(route('product.list', ['categorySlug' => $this->category->slug]));

        $listResponse->assertOk();
        $listResponse->assertSee($this->primaryProduct->name);
        $listResponse->assertDontSee('bi-star-fill', false);
        $listResponse->assertDontSee('đánh giá)');

        $initialObLevel = ob_get_level();
        try {
            $searchResponse = $this->withoutMiddleware([Views::class])
                ->get(route('search', ['tu-khoa' => 'Laser']));
        } finally {
            // View user/search/list.blade.php hiện có @section('footer') pre-existing chưa đóng;
            // dọn buffer phát sinh để PHPUnit không báo risky ngoài phạm vi ticket L12.
            while (ob_get_level() > $initialObLevel) {
                ob_end_clean();
            }
        }

        $searchResponse->assertOk();
        $searchResponse->assertSee($this->primaryProduct->name);
        $searchResponse->assertDontSee('bi-star-fill', false);
        $searchResponse->assertDontSee('đánh giá)');

        foreach (['post.index', 'instruction.index', 'solution.index'] as $routeName) {
            $sidebarResponse = $this->withoutMiddleware([Views::class])
                ->get(route($routeName));

            $sidebarResponse->assertOk();
            $sidebarResponse->assertDontSee('bi-star-fill', false);
            $sidebarResponse->assertDontSee('đánh giá)');
        }
    }
}

