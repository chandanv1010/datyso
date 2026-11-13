<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\Instruction;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\Solution;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * Kiểm thử cập nhật số hotline 0968945633 và email support@datyso.vn trên toàn bộ dự án.
 */
class ContactInfoUpdateTest extends TestCase
{
    protected Category $category;

    protected Product $product;

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

    protected function setUp(): void
    {
        parent::setUp();

        $exitCode = Artisan::call('migrate', ['--force' => true]);
        if ($exitCode !== 0 || ! Schema::hasTable('m_user')) {
            throw new \RuntimeException('Migrate failed ('.$exitCode.'): '.Artisan::output());
        }

        $userId = (int) (DB::table('m_user')->value('id') ?? 1);

        $this->category = Category::create([
            'name' => 'Máy Hàn Laser',
            'slug' => 'may-han-laser',
            'status' => 1,
            'user_id' => $userId,
            'image' => 'category/sample.jpg',
        ]);

        $this->product = Product::create([
            'name' => 'Máy Hàn Laser QG-SC 1500W',
            'slug' => 'may-han-laser-qg-sc-1500w',
            'description' => '<p>Máy hàn laser công suất 1500W.</p>',
            'status' => 1,
            'del_flg' => 1,
            'user_id' => $userId,
            'price' => 0,
        ]);

        ProductCategory::create([
            'product_id' => $this->product->id,
            'category_id' => $this->category->id,
        ]);

        ProductImage::create([
            'product_id' => $this->product->id,
            'image' => 'product/sample.jpg',
        ]);

        View::share([
            'categories' => Category::where([['parent_id', null], ['status', 1]])->orderBy('id', 'ASC')->get(),
            'socials' => collect(),
        ]);
    }

    /**
     * Kiểm tra trang liên hệ hiển thị hotline mới và email mới, không còn thông tin cũ.
     */
    public function test_contact_page_displays_updated_hotline_and_email(): void
    {
        $response = $this->get(route('contact.index'));
        $response->assertOk();

        $content = $response->getContent();
        $this->assertStringContainsString('0968945633', $content);
        $this->assertStringContainsString('support@datyso.vn', $content);

        // Không còn hotline và email cũ
        $this->assertStringNotContainsString('0963.535.196', $content);
        $this->assertStringNotContainsString('0963535196', $content);
        $this->assertStringNotContainsString('Datysojsc@gmail.com', $content);
        $this->assertStringNotContainsString('datysojsc@gmail.com', $content);
    }

    /**
     * Kiểm tra footer và form nhận báo giá hiển thị đúng số hotline mới và liên kết tel:0968945633.
     */
    public function test_footer_and_lead_form_display_updated_hotline_and_email(): void
    {
        $response = $this->get(route('product.detail', [
            'categorySlug' => $this->category->slug,
            'productSlug' => $this->product->slug,
        ]));
        $response->assertOk();

        $content = $response->getContent();
        $this->assertStringContainsString('tel:0968945633', $content);
        $this->assertStringContainsString('0968945633', $content);
        $this->assertStringContainsString('support@datyso.vn', $content);

        $this->assertStringNotContainsString('tel:0963535196', $content);
        $this->assertStringNotContainsString('0963.535.196', $content);
        $this->assertStringNotContainsString('Datysojsc@gmail.com', $content);
    }

    /**
     * Kiểm tra Address model tự động chuyển đổi hotline và email cũ sang thông tin mới.
     */
    public function test_address_model_accessors_normalize_legacy_contact_info(): void
    {
        $address = new Address([
            'name' => 'Chi nhánh test',
            'address' => 'Hà Nội',
            'phone' => '0963535196',
            'hotline' => '0963.535.196',
            'email' => 'Datysojsc@gmail.com',
        ]);

        $this->assertSame('0968945633', $address->phone);
        $this->assertSame('0968945633', $address->hotline);
        $this->assertSame('support@datyso.vn', $address->email);
    }

    /**
     * Kiểm tra Post, Instruction, Solution models tự động chuẩn hóa hotline và email trong nội dung.
     */
    public function test_content_models_normalize_legacy_hotline_and_email(): void
    {
        $legacyContent = '<p>Hotline: 0963.535.196 hoặc 0963535196. Email: Datysojsc@gmail.com</p>';

        $post = new Post(['content' => $legacyContent]);
        $instruction = new Instruction(['content' => $legacyContent]);
        $solution = new Solution(['content' => $legacyContent]);

        foreach ([$post, $instruction, $solution] as $model) {
            $this->assertStringContainsString('0968945633', $model->content);
            $this->assertStringContainsString('support@datyso.vn', $model->content);
            $this->assertStringNotContainsString('0963.535.196', $model->content);
            $this->assertStringNotContainsString('0963535196', $model->content);
            $this->assertStringNotContainsString('Datysojsc@gmail.com', $model->content);
        }
    }
}
