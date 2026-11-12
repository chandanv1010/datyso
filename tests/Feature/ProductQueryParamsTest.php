<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\Views as SiteViews;
use App\Models\ViewsPost;
use App\Models\ViewsProduct;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * Kiểm thử xử lý tham số truy vấn lạ (?utm_source, ?gclid, ?a=1...) trên route/controller/middleware
 * và kiểm tra cấu hình tắt APP_DEBUG để trang lỗi không lộ mã nguồn trên local & hosting.
 */
class ProductQueryParamsTest extends TestCase
{
    protected Category $parentCategory;
    protected Category $childCategory;
    protected Product $product;
    protected Post $post;

    /**
     * Dùng SQLite in-memory để chạy test độc lập trên cả local và hosting.
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
     * Khởi tạo dữ liệu mẫu danh mục cha/con, sản phẩm và bài viết.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $exitCode = Artisan::call('migrate', ['--force' => true]);
        if ($exitCode !== 0 || ! Schema::hasTable('m_user')) {
            throw new \RuntimeException('Migrate failed (' . $exitCode . '): ' . Artisan::output());
        }

        $this->seedSampleContent();

        View::share([
            'categories' => Category::where([['parent_id', null], ['status', 1]])->orderBy('id', 'ASC')->get(),
            'socials' => collect(),
        ]);
    }

    /**
     * Tạo danh mục cha/con, sản phẩm và bài viết đang hoạt động cho bộ test.
     */
    private function seedSampleContent(): void
    {
        $userId = (int) (DB::table('m_user')->value('id') ?? 1);

        $this->parentCategory = Category::create([
            'name' => 'Máy Hàn Laser',
            'slug' => 'may-han-laser',
            'status' => 1,
            'user_id' => $userId,
            'image' => 'category/parent.jpg',
        ]);

        $this->childCategory = Category::create([
            'name' => 'Máy Hàn Laser Cầm Tay',
            'slug' => 'may-han-laser-cam-tay',
            'parent_id' => $this->parentCategory->id,
            'status' => 1,
            'user_id' => $userId,
            'image' => 'category/child.jpg',
        ]);

        $this->product = Product::create([
            'name' => 'Máy Hàn Laser QG-SC 1500W',
            'slug' => 'may-han-laser-qg-sc-1500w',
            'description' => '<p>Máy hàn laser công nghiệp QG-SC 1500W.</p>',
            'status' => 1,
            'del_flg' => 1,
            'user_id' => $userId,
            'price' => 0,
        ]);

        ProductCategory::create([
            'product_id' => $this->product->id,
            'category_id' => $this->childCategory->id,
        ]);

        ProductImage::create([
            'product_id' => $this->product->id,
            'image' => 'product/qg-sc-1500w.jpg',
        ]);

        $this->post = Post::create([
            'name' => 'Hướng dẫn vận hành máy hàn laser an toàn',
            'slug' => 'huong-dan-van-hanh-may-han-laser',
            'content' => '<p>Nội dung hướng dẫn vận hành máy hàn laser.</p>',
            'status' => 1,
            'user_id' => $userId,
            'image' => 'post/guide.jpg',
        ]);
    }

    /**
     * Đảm bảo trang chi tiết sản phẩm bỏ qua mọi tham số lạ (?a=1, ?utm_..., ?gclid=...) và ghi nhận lượt xem đúng.
     */
    public function test_product_detail_ignores_tracking_and_arbitrary_query_parameters(): void
    {
        $this->withoutExceptionHandling();

        $baseUrl = '/san-pham/' . $this->childCategory->slug . '/chi-tiet/' . $this->product->slug;

        $responseSimple = $this->get($baseUrl . '?a=1');
        $responseSimple->assertOk();
        $responseSimple->assertSee($this->product->name);

        $html = $responseSimple->getContent();
        $this->assertStringNotContainsString('?a=1', $html);

        $this->assertSame(1, ViewsProduct::where('product_id', $this->product->id)->count());
        $this->assertSame(1, SiteViews::count());

        $utmUrl = $baseUrl . '?utm_source=google&utm_medium=cpc&utm_campaign=laser_2026&gclid=EAIaIQobChMI&fbclid=IwAR0&foo=bar';
        $responseAds = $this->get($utmUrl);
        $responseAds->assertOk();
        $responseAds->assertSee($this->product->name);

        $adsHtml = $responseAds->getContent();
        $this->assertStringNotContainsString('utm_source=google', $adsHtml);
        $this->assertStringNotContainsString('gclid=EAIaIQobChMI', $adsHtml);
    }

    /**
     * Đảm bảo redirect 301 từ danh mục cha sang danh mục con chuẩn không crash và giữ được query tracking.
     */
    public function test_product_detail_parent_category_redirects_301_without_crashing_on_query_params(): void
    {
        $parentUrl = '/san-pham/' . $this->parentCategory->slug . '/chi-tiet/' . $this->product->slug . '?gclid=abc123&a=1';

        $response = $this->get($parentUrl);

        $response->assertStatus(301);
        $location = (string) $response->headers->get('Location');
        $this->assertStringContainsString('/san-pham/' . $this->childCategory->slug . '/chi-tiet/' . $this->product->slug, $location);
        $this->assertStringContainsString('gclid=abc123', $location);
        $this->assertStringContainsString('a=1', $location);
    }

    /**
     * Đảm bảo trang danh mục sản phẩm hoạt động ổn định với UTM, tham số lạ và tham số page không hợp lệ.
     */
    public function test_category_listing_ignores_utm_and_malformed_page_parameters(): void
    {
        $this->withoutExceptionHandling();

        $catUrl = '/san-pham/' . $this->childCategory->slug;

        $responseUtm = $this->get($catUrl . '?utm_source=google&gclid=xyz&a=1&page=abc');
        $responseUtm->assertOk();
        $responseUtm->assertSee($this->product->name);

        $responseArrayPage = $this->get($catUrl . '?page[]=1&utm_medium=cpc');
        $responseArrayPage->assertOk();
        $responseArrayPage->assertSee($this->product->name);
    }

    /**
     * Đảm bảo trang chi tiết tin tức cũng bỏ qua tham số lạ khi middleware Views phân giải slug.
     */
    public function test_post_detail_ignores_query_parameters_and_records_view(): void
    {
        $this->withoutExceptionHandling();

        $postUrl = '/tin-tuc/' . $this->post->slug . '/chi-tiet?utm_source=facebook&fbclid=test123&a=1';
        $response = $this->get($postUrl);

        $response->assertOk();
        $response->assertSee($this->post->name);
        $this->assertSame(1, ViewsPost::where('post_id', $this->post->id)->count());
    }

    /**
     * Đảm bảo slug không tồn tại (kèm query string) không bao giờ gây lỗi 500 "Attempt to read property id on null".
     */
    public function test_nonexistent_product_or_post_slug_with_query_params_does_not_throw_500(): void
    {
        $this->withoutExceptionHandling();

        $missingProduct = $this->get('/san-pham/' . $this->childCategory->slug . '/chi-tiet/san-pham-khong-ton-tai?a=1&gclid=123');
        $missingProduct->assertRedirect(route('index'));

        $missingPost = $this->get('/tin-tuc/bai-viet-khong-ton-tai/chi-tiet?utm_source=google&a=1');
        $missingPost->assertRedirect(route('index'));

        $this->assertSame(0, ViewsProduct::count());
        $this->assertSame(0, ViewsPost::count());
    }

    /**
     * Đảm bảo tham số tìm kiếm dạng mảng (?tu-khoa[]=...) không gây lỗi Array to string conversion.
     */
    public function test_search_ignores_malformed_array_keyword_safely(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/tim-kiem?tu-khoa[]=laser&a=1');
        $response->assertRedirect(route('index'));
    }

    /**
     * Đảm bảo APP_DEBUG tắt mặc định, domain hosting ép tắt debug và trang lỗi 500 không lộ mã nguồn/stack trace.
     */
    public function test_app_debug_is_disabled_on_hosting_and_error_page_hides_source_code(): void
    {
        $backupServer = $_SERVER;
        $backupEnv = $_ENV;
        $backupGetenvDebug = getenv('APP_DEBUG');
        $backupGetenvEnv = getenv('APP_ENV');

        try {
            // 1. Giả lập môi trường hosting datyso.com ngay cả khi .env cũ lỡ bật APP_DEBUG=true.
            $_SERVER['HTTP_HOST'] = 'datyso.com';
            $_ENV['APP_ENV'] = $_SERVER['APP_ENV'] = 'local';
            $_ENV['APP_DEBUG'] = $_SERVER['APP_DEBUG'] = 'true';
            putenv('APP_ENV=local');
            putenv('APP_DEBUG=true');

            $hostingConfig = require base_path('config/app.php');
            $this->assertFalse($hostingConfig['debug']);

            // 2. Kiểm tra mặc định khi APP_DEBUG=false ở local.
            $_SERVER['HTTP_HOST'] = '127.0.0.1:8000';
            $_ENV['APP_DEBUG'] = $_SERVER['APP_DEBUG'] = 'false';
            putenv('APP_DEBUG=false');

            $localDisabledConfig = require base_path('config/app.php');
            $this->assertFalse($localDisabledConfig['debug']);
        } finally {
            $_SERVER = $backupServer;
            $_ENV = $backupEnv;
            putenv($backupGetenvDebug === false ? 'APP_DEBUG' : 'APP_DEBUG=' . $backupGetenvDebug);
            putenv($backupGetenvEnv === false ? 'APP_ENV' : 'APP_ENV=' . $backupGetenvEnv);
        }

        // 3. Đăng ký route tạm phát sinh exception để xác minh response 500 khi debug=false không lộ mã nguồn.
        Route::get('/_test-internal-error-page', function () {
            throw new \RuntimeException('SENSITIVE_DB_PASSWORD_OR_TRACE_MARKER');
        });

        config(['app.debug' => false]);
        $errorResponse = $this->get('/_test-internal-error-page');
        $errorResponse->assertStatus(500);

        $body = $errorResponse->getContent();
        $this->assertStringNotContainsString('SENSITIVE_DB_PASSWORD_OR_TRACE_MARKER', $body);
        $this->assertStringNotContainsString('RuntimeException', $body);
        $this->assertStringNotContainsString('ProductQueryParamsTest', $body);
        $this->assertStringNotContainsString('vendor/', $body);
    }
}
