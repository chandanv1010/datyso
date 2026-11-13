<?php

namespace Tests\Feature;

use App\Http\Middleware\Views;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\System;
use App\Models\User;
use App\Services\LeadService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class LeadFormTest extends TestCase
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
        $app['config']->set('mail.default', 'array');
        $app['config']->set('queue.default', 'sync');
        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $exitCode = Artisan::call('migrate', ['--force' => true]);
        if ($exitCode !== 0 || !Schema::hasTable('m_user')) {
            throw new \RuntimeException('Migrate failed (' . $exitCode . '): ' . Artisan::output());
        }

        $this->seedSampleCatalog();

        // AppServiceProvider::boot() chạy trước migrate nên share lại categories/socials từ SQLite test DB
        View::share([
            'categories' => Category::where([['parent_id', null], ['status', 1]])->orderBy('id', 'ASC')->get(),
            'socials' => collect(),
        ]);
    }

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

        $this->product = Product::create([
            'name' => 'Máy Hàn Laser Fiber QG-SC 1500W',
            'slug' => 'may-han-laser-qg-sc-1500w',
            'description' => '<p>Máy hàn laser công suất 1500W chính hãng DATYSO.</p>',
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
    }

    public function test_category_page_renders_short_lead_form_with_autofilled_category(): void
    {
        $response = $this->withoutMiddleware([Views::class])
            ->get(route('product.list', ['categorySlug' => $this->category->slug]));

        $response->assertOk();
        $response->assertSee('id="lead-quote-section"', false);
        $response->assertSee('name="source_type" value="category"', false);
        $response->assertSee('name="category_id" value="' . $this->category->id . '"', false);
        $response->assertSee('name="source_name" value="' . e($this->category->name) . '"', false);
        $response->assertSee('hp_company_fax', false);
        $response->assertSee('generate_lead', false);
        $response->assertSee('DATYSO sẽ gọi lại trong giờ làm việc');
    }

    public function test_product_page_renders_inline_lead_form_and_scroll_cta(): void
    {
        $response = $this->withoutMiddleware([Views::class])
            ->get(route('product.detail', [
                'categorySlug' => $this->category->slug,
                'productSlug' => $this->product->slug,
            ]));

        $response->assertOk();
        $response->assertSee('href="#lead-quote-section"', false);
        $response->assertSee('id="lead-quote-section"', false);
        $response->assertSee('name="source_type" value="product"', false);
        $response->assertSee('name="product_id" value="' . $this->product->id . '"', false);
        $response->assertSee('name="source_name" value="' . e($this->product->name) . '"', false);
        $response->assertSee('generate_lead', false);
    }

    public function test_contact_page_has_title_h1_and_native_lead_form_with_product_context(): void
    {
        $response = $this->withoutMiddleware([Views::class])
            ->get(route('contact.index', ['san-pham' => $this->product->slug]));

        $response->assertOk();
        $response->assertSee('<title>Liên hệ báo giá ' . e($this->product->name) . ' | DATYSO</title>', false);
        $response->assertSee('<h1', false);
        $response->assertSee('name="product_id" value="' . $this->product->id . '"', false);
        $response->assertSee('name="source_name" value="' . e($this->product->name) . '"', false);
        $response->assertDontSee('forms.gle');
        $response->assertDontSee('hsforms.net');
    }

    public function test_lead_submission_saves_to_admin_without_sending_sales_email_and_syncs_google_sheet(): void
    {
        Mail::fake();
        Http::fake([
            'https://script.google.com/*' => Http::response(['ok' => true], 200),
        ]);

        // Kể cả khi DB còn dữ liệu lead_sales_email cũ, hệ thống vẫn không gửi email về Sales
        System::updateOrInsert(['keyword' => 'lead_sales_email'], ['content' => 'sales1@datyso.com, sales2@datyso.com']);
        System::updateOrInsert(['keyword' => 'lead_google_sheet_webhook'], ['content' => 'https://script.google.com/macros/s/test-webhook/exec']);

        /** @var LeadService $leadService */
        $leadService = app(LeadService::class);
        $ts = time() - 5;

        $response = $this->postJson(route('contact.register'), [
            'username' => 'Nguyễn Văn An',
            'phone' => '+84 968.945.633',
            'province' => 'Hà Nội',
            'demand' => '', // Không bắt buộc
            'source_type' => 'product',
            'product_id' => $this->product->id,
            'category_id' => $this->category->id,
            'source_name' => $this->product->name,
            'source_url' => 'https://datyso.com/san-pham/may-han-laser-cam-tay/chi-tiet/may-han-laser-qg-sc-1500w',
            'hp_company_fax' => '',
            '_form_ts' => (string) $ts,
            '_form_token' => $leadService->makeFormToken($ts),
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => true,
                'duplicate' => false,
                'message' => LeadService::AUTO_REPLY_MESSAGE,
                'lead' => [
                    'source_type' => 'product',
                    'source_name' => $this->product->name,
                    'product_id' => $this->product->id,
                    'category_id' => $this->category->id,
                    'province' => 'Hà Nội',
                ],
            ]);

        $this->assertDatabaseHas('m_contact', [
            'username' => 'Nguyễn Văn An',
            'phone' => '0968945633',
            'province' => 'Hà Nội',
            'email' => null,
            'product_id' => $this->product->id,
            'category_id' => $this->category->id,
            'source_type' => 'product',
            'source_name' => $this->product->name,
        ]);

        Mail::assertNothingSent();

        Http::assertSent(function ($request) {
            return str_starts_with($request->url(), 'https://script.google.com/macros/s/test-webhook/exec')
                && $request['phone'] === '0968945633'
                && $request['source_name'] === $this->product->name;
        });
    }

    public function test_honeypot_and_invalid_timestamp_block_spam(): void
    {
        Mail::fake();
        /** @var LeadService $leadService */
        $leadService = app(LeadService::class);
        $validTs = time() - 5;

        // 1. Bot điền honeypot
        $hpResponse = $this->postJson(route('contact.register'), [
            'username' => 'Bot User',
            'phone' => '0987654321',
            'hp_company_fax' => 'Spam Corp',
            '_form_ts' => (string) $validTs,
            '_form_token' => $leadService->makeFormToken($validTs),
        ]);
        $hpResponse->assertStatus(422);

        // 2. Bot gửi quá nhanh (< 1 giây)
        $nowTs = time();
        $fastResponse = $this->postJson(route('contact.register'), [
            'username' => 'Fast Bot',
            'phone' => '0987654321',
            'hp_company_fax' => '',
            '_form_ts' => (string) $nowTs,
            '_form_token' => $leadService->makeFormToken($nowTs),
        ]);
        $fastResponse->assertStatus(422);

        // 3. Chứa URL spam trong Họ tên
        $urlResponse = $this->postJson(route('contact.register'), [
            'username' => 'Mua ngay https://spam.example',
            'phone' => '0987654321',
            'hp_company_fax' => '',
            '_form_ts' => (string) $validTs,
            '_form_token' => $leadService->makeFormToken($validTs),
        ]);
        $urlResponse->assertStatus(422);

        $this->assertSame(0, Contact::count());
        Mail::assertNothingSent();
    }

    public function test_recaptcha_v3_verification_when_keys_configured(): void
    {
        Mail::fake();
        System::updateOrInsert(['keyword' => 'lead_recaptcha_site_key'], ['content' => 'site-key-123']);
        System::updateOrInsert(['keyword' => 'lead_recaptcha_secret_key'], ['content' => 'secret-key-123']);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::sequence()
                ->push(['success' => true, 'score' => 0.1, 'action' => 'generate_lead'], 200)
                ->push(['success' => true, 'score' => 0.9, 'action' => 'generate_lead'], 200),
        ]);

        /** @var LeadService $leadService */
        $leadService = app(LeadService::class);
        $ts = time() - 5;

        // Low score rejected
        $lowScore = $this->postJson(route('contact.register'), [
            'username' => 'Suspect Bot',
            'phone' => '0912345678',
            'hp_company_fax' => '',
            '_form_ts' => (string) $ts,
            '_form_token' => $leadService->makeFormToken($ts),
            'g_recaptcha_response' => 'low-score-token',
        ]);
        $lowScore->assertStatus(422);
        $this->assertSame(0, Contact::count());

        // High score accepted
        $highScore = $this->postJson(route('contact.register'), [
            'username' => 'Khách Thật',
            'phone' => '0912345678',
            'hp_company_fax' => '',
            '_form_ts' => (string) $ts,
            '_form_token' => $leadService->makeFormToken($ts),
            'g_recaptcha_response' => 'good-token',
        ]);
        $highScore->assertOk()->assertJson(['status' => true]);
        $this->assertSame(1, Contact::count());
    }

    public function test_duplicate_submit_within_45_seconds_does_not_create_duplicate_rows(): void
    {
        Mail::fake();
        /** @var LeadService $leadService */
        $leadService = app(LeadService::class);
        $ts = time() - 5;

        $payload = [
            'username' => 'Trần Thị Bình',
            'phone' => '0977112233',
            'source_type' => 'category',
            'category_id' => $this->category->id,
            'source_name' => $this->category->name,
            'hp_company_fax' => '',
            '_form_ts' => (string) $ts,
            '_form_token' => $leadService->makeFormToken($ts),
        ];

        $this->postJson(route('contact.register'), $payload)
            ->assertOk()
            ->assertJson(['status' => true, 'duplicate' => false]);

        $this->postJson(route('contact.register'), $payload)
            ->assertOk()
            ->assertJson(['status' => true, 'duplicate' => true]);

        $this->assertSame(1, Contact::count());
    }

    public function test_admin_contact_list_and_system_settings_work_correctly(): void
    {
        Mail::fake();

        $contact = Contact::create([
            'username' => 'Lê Minh Tâm',
            'phone' => '0909123456',
            'province' => 'Bình Dương',
            'demand' => 'Cắt thép 10mm',
            'source_type' => 'category',
            'category_id' => $this->category->id,
            'source_name' => $this->category->name,
        ]);

        $admin = User::first();
        $listRes = $this->actingAs($admin, 'admin')->get(route('admin.contact.index'));
        $listRes->assertOk();
        $listRes->assertSee('Lê Minh Tâm');
        $listRes->assertSee('Bình Dương');
        $listRes->assertSee('Cắt thép 10mm');
        $listRes->assertSee('Chưa đọc: 1');
        $listRes->assertDontSee('Cảnh báo gửi Email Sales');
        $listRes->assertDontSee('Mail Sales');

        // Đánh dấu đã đọc & kiểm tra Admin tạo liên hệ thủ công không gửi mail
        $this->actingAs($admin, 'admin')
            ->post(route('admin.contact.status', ['id' => $contact->id]))
            ->assertRedirect(route('admin.contact.index'));
        $this->assertSame(1, (int) $contact->fresh()->is_read);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.contact.store'), [
                'username' => 'Khách Admin Nhập',
                'phone' => '0911223344',
                'email' => '',
                'content' => 'Yêu cầu tư vấn tại showroom',
            ])
            ->assertRedirect(route('admin.contact.index'));
        $this->assertDatabaseHas('m_contact', ['phone' => '0911223344', 'username' => 'Khách Admin Nhập']);
        Mail::assertNothingSent();

        $systemRes = $this->actingAs($admin, 'admin')->get(route('admin.system.index'));
        $systemRes->assertOk();
        $systemRes->assertSee('lead_google_sheet_webhook', false);
        $systemRes->assertSee('lead_recaptcha_site_key', false);
        $systemRes->assertDontSee('lead_sales_email', false);
        $systemRes->assertDontSee('mail_host', false);
        $systemRes->assertDontSee('Gửi Email Test Ngay');

        // Verify SystemController::store properly updates existing key instead of inserting duplicates
        $this->actingAs($admin, 'admin')->post(route('admin.system.store'), [
            'config' => ['lead_google_sheet_webhook' => 'https://script.google.com/macros/s/first/exec'],
        ])->assertRedirect();

        $this->actingAs($admin, 'admin')->post(route('admin.system.store'), [
            'config' => ['lead_google_sheet_webhook' => 'https://script.google.com/macros/s/updated/exec'],
        ])->assertRedirect();

        $this->assertSame(1, System::where('keyword', 'lead_google_sheet_webhook')->count());
        $this->assertSame('https://script.google.com/macros/s/updated/exec', System::where('keyword', 'lead_google_sheet_webhook')->value('content'));
    }

    public function test_lead_submission_never_calls_mailer_even_if_configured(): void
    {
        System::updateOrInsert(['keyword' => 'lead_sales_email'], ['content' => 'sales@datyso.com']);

        Mail::shouldReceive('to')->never();
        Mail::shouldReceive('mailer')->never();

        /** @var LeadService $leadService */
        $leadService = app(LeadService::class);
        $ts = time() - 5;

        $response = $this->postJson(route('contact.register'), [
            'username' => 'Vũ Văn Minh',
            'phone' => '0966554433',
            'province' => 'Hải Phòng',
            'source_type' => 'category',
            'category_id' => $this->category->id,
            'source_name' => $this->category->name,
            'hp_company_fax' => '',
            '_form_ts' => (string) $ts,
            '_form_token' => $leadService->makeFormToken($ts),
        ]);

        $response->assertOk()->assertJson([
            'status' => true,
            'message' => LeadService::AUTO_REPLY_MESSAGE,
        ]);

        $this->assertDatabaseHas('m_contact', [
            'username' => 'Vũ Văn Minh',
            'phone' => '0966554433',
            'province' => 'Hải Phòng',
        ]);
    }

    public function test_non_js_fallback_post_redirects_with_success_and_datalayer_flash(): void
    {
        Mail::fake();
        /** @var LeadService $leadService */
        $leadService = app(LeadService::class);
        $ts = time() - 5;

        $fromUrl = route('product.list', ['categorySlug' => $this->category->slug]);
        $response = $this->from($fromUrl)
            ->post(route('contact.register'), [
                'username' => 'Đỗ Thị Hoa',
                'phone' => '0933221100',
                'province' => 'TP. Hồ Chí Minh',
                'demand' => 'Hàn khung nhôm mỏng 1.5mm',
                'source_type' => 'category',
                'category_id' => $this->category->id,
                'source_name' => $this->category->name,
                'hp_company_fax' => '',
                '_form_ts' => (string) $ts,
                '_form_token' => $leadService->makeFormToken($ts),
            ]);

        $response->assertRedirect($fromUrl . '#lead-quote-section');
        $response->assertSessionHas('messageSuccess', LeadService::AUTO_REPLY_MESSAGE);
        $response->assertSessionHas('leadSuccessData');

        $this->assertDatabaseHas('m_contact', [
            'username' => 'Đỗ Thị Hoa',
            'phone' => '0933221100',
            'province' => 'TP. Hồ Chí Minh',
            'demand' => 'Hàn khung nhôm mỏng 1.5mm',
        ]);
        Mail::assertNothingSent();
    }

    public function test_legacy_m_contact_table_auto_heals_schema_on_submit(): void
    {
        // Giả lập bảng m_contact cũ trên hosting trước khi chạy migrate
        Schema::dropIfExists('m_contact');
        Schema::create('m_contact', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email');
            $table->string('phone', 12);
            $table->longText('content')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedTinyInteger('is_read')->default(0);
            $table->unsignedTinyInteger('is_supported')->default(0);
            $table->timestamps();
        });

        Mail::fake();
        /** @var LeadService $leadService */
        $leadService = app(LeadService::class);
        $ts = time() - 5;

        Contact::ensureLeadSchema(true);

        $res = $this->postJson(route('contact.register'), [
            'username' => 'Phạm Quốc Hùng',
            'phone' => '0988777666',
            'province' => 'Đà Nẵng',
            'demand' => 'Tư vấn máy laser 3kW',
            'source_type' => 'product',
            'product_id' => $this->product->id,
            'source_name' => $this->product->name,
            'hp_company_fax' => '',
            '_form_ts' => (string) $ts,
            '_form_token' => $leadService->makeFormToken($ts),
        ]);

        $res->assertOk()->assertJson(['status' => true]);
        $this->assertTrue(Schema::hasColumn('m_contact', 'province'));
        $this->assertTrue(Schema::hasColumn('m_contact', 'demand'));
        $this->assertDatabaseHas('m_contact', [
            'username' => 'Phạm Quốc Hùng',
            'province' => 'Đà Nẵng',
        ]);
        Mail::assertNothingSent();
    }
}
