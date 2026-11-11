<?php

namespace Tests\Feature;

use App\Http\Middleware\Views;
use App\Mail\ContactMail;
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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
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

    public function test_lead_submission_saves_to_db_sends_sales_email_and_syncs_google_sheet(): void
    {
        Mail::fake();
        Http::fake([
            'https://script.google.com/*' => Http::response(['ok' => true], 200),
        ]);

        System::updateOrInsert(['keyword' => 'lead_sales_email'], ['content' => 'sales1@datyso.com, sales2@datyso.com']);
        System::updateOrInsert(['keyword' => 'lead_google_sheet_webhook'], ['content' => 'https://script.google.com/macros/s/test-webhook/exec']);

        /** @var LeadService $leadService */
        $leadService = app(LeadService::class);
        $ts = time() - 5;

        $response = $this->postJson(route('contact.register'), [
            'username' => 'Nguyễn Văn An',
            'phone' => '+84 963.535.196',
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
            'phone' => '0963535196',
            'province' => 'Hà Nội',
            'email' => null,
            'product_id' => $this->product->id,
            'category_id' => $this->category->id,
            'source_type' => 'product',
            'source_name' => $this->product->name,
        ]);

        Mail::assertSent(ContactMail::class, function (ContactMail $mail) {
            return $mail->hasTo('sales1@datyso.com') && $mail->hasTo('sales2@datyso.com');
        });

        Http::assertSent(function ($request) {
            return str_starts_with($request->url(), 'https://script.google.com/macros/s/test-webhook/exec')
                && $request['phone'] === '0963535196'
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
        Contact::create([
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

        $systemRes = $this->actingAs($admin, 'admin')->get(route('admin.system.index'));
        $systemRes->assertOk();
        $systemRes->assertSee('lead_sales_email', false);
        $systemRes->assertSee('lead_google_sheet_webhook', false);
        $systemRes->assertSee('lead_recaptcha_site_key', false);

        // Verify SystemController::store properly updates existing key instead of inserting broken attributes
        $this->actingAs($admin, 'admin')->post(route('admin.system.store'), [
            'config' => ['lead_sales_email' => 'sales-first@datyso.com'],
        ])->assertRedirect();

        $this->actingAs($admin, 'admin')->post(route('admin.system.store'), [
            'config' => ['lead_sales_email' => 'sales-updated@datyso.com'],
        ])->assertRedirect();

        $this->assertSame(1, System::where('keyword', 'lead_sales_email')->count());
        $this->assertSame('sales-updated@datyso.com', System::where('keyword', 'lead_sales_email')->value('content'));
    }

    public function test_mail_failure_does_not_block_lead_saving_or_success_response(): void
    {
        System::updateOrInsert(['keyword' => 'lead_sales_email'], ['content' => 'sales@datyso.com']);

        Mail::shouldReceive('to')
            ->once()
            ->andThrow(new \RuntimeException('SMTP connection timed out'));

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
        $this->assertTrue(Schema::hasColumn('m_contact', 'mail_status'));
        $this->assertTrue(Schema::hasColumn('m_contact', 'mail_error'));
        $this->assertTrue(Schema::hasColumn('m_contact', 'mail_sent_at'));
        $this->assertDatabaseHas('m_contact', [
            'username' => 'Phạm Quốc Hùng',
            'province' => 'Đà Nẵng',
        ]);
    }

    public function test_lead_mail_runtime_config_resolves_admin_smtp_and_replaces_example_from(): void
    {
        Config::set('mail.default', 'log');
        Config::set('mail.from.address', 'hello@example.com');
        Config::set('mail.from.name', 'Example');

        System::updateOrInsert(['keyword' => 'lead_sales_email'], ['content' => 'nv09.mkt@datyso.vn']);
        System::updateOrInsert(['keyword' => 'mail_mailer'], ['content' => 'auto']);
        System::updateOrInsert(['keyword' => 'mail_host'], ['content' => 'mail.datyso.com']);
        System::updateOrInsert(['keyword' => 'mail_port'], ['content' => '465']);
        System::updateOrInsert(['keyword' => 'mail_encryption'], ['content' => 'ssl']);
        System::updateOrInsert(['keyword' => 'mail_username'], ['content' => 'noreply@datyso.com']);
        System::updateOrInsert(['keyword' => 'mail_password'], ['content' => Crypt::encryptString('MatKhauSmtp@2026')]);
        System::updateOrInsert(['keyword' => 'mail_from_address'], ['content' => null]);

        /** @var LeadService $leadService */
        $leadService = app(LeadService::class);
        $resolved = $leadService->resolveMailRuntimeConfig();

        $this->assertSame('smtp', $resolved['mailer']);
        $this->assertSame('mail.datyso.com', $resolved['host']);
        $this->assertSame(465, $resolved['port']);
        $this->assertSame('ssl', $resolved['encryption']);
        $this->assertSame('noreply@datyso.com', $resolved['username']);
        $this->assertSame('MatKhauSmtp@2026', $resolved['password']);
        $this->assertSame('noreply@datyso.com', $resolved['from_address']);
        $this->assertSame('DATYSO Website', $resolved['from_name']);
    }

    public function test_admin_system_encrypts_mail_password_preserves_on_blank_and_supports_test_mail(): void
    {
        $admin = User::first();

        $this->actingAs($admin, 'admin')->post(route('admin.system.store'), [
            'config' => [
                'lead_sales_email' => 'nv09.mkt@datyso.vn',
                'mail_mailer' => 'smtp',
                'mail_host' => 'smtp.gmail.com',
                'mail_port' => '587',
                'mail_encryption' => 'tls',
                'mail_username' => 'datysoweb@gmail.com',
                'mail_password' => 'my-app-pass-16ch',
                'mail_from_address' => 'datysoweb@gmail.com',
                'mail_from_name' => 'DATYSO Hotline',
            ],
        ])->assertRedirect();

        $storedEncrypted = System::where('keyword', 'mail_password')->value('content');
        $this->assertNotEmpty($storedEncrypted);
        $this->assertNotSame('my-app-pass-16ch', $storedEncrypted);
        $this->assertSame('my-app-pass-16ch', Crypt::decryptString($storedEncrypted));

        // Trang cấu hình không được lộ mật khẩu ra HTML và phải báo đã lưu
        $systemPage = $this->actingAs($admin, 'admin')->get(route('admin.system.index'));
        $systemPage->assertOk();
        $systemPage->assertDontSee('my-app-pass-16ch', false);
        $systemPage->assertDontSee($storedEncrypted, false);
        $systemPage->assertSee('Đã lưu mật khẩu SMTP');

        // Lưu lại khi ô mật khẩu để trống -> giữ nguyên mật khẩu cũ
        $this->actingAs($admin, 'admin')->post(route('admin.system.store'), [
            'config' => [
                'lead_sales_email' => 'nv09.mkt@datyso.vn, sales@datyso.com',
                'mail_password' => '',
            ],
        ])->assertRedirect();

        $this->assertSame($storedEncrypted, System::where('keyword', 'mail_password')->value('content'));

        // Test nút Gửi Email Test Ngay
        Mail::fake();
        $testRes = $this->actingAs($admin, 'admin')->postJson(route('admin.system.testMail'), [
            'test_email' => 'nv09.mkt@datyso.vn',
            'config' => [
                'lead_sales_email' => 'nv09.mkt@datyso.vn',
                'mail_mailer' => 'smtp',
                'mail_host' => 'smtp.gmail.com',
                'mail_port' => '587',
                'mail_encryption' => 'tls',
                'mail_username' => 'datysoweb@gmail.com',
                'mail_password' => '',
                'mail_from_address' => 'datysoweb@gmail.com',
            ],
        ]);

        $testRes->assertOk()->assertJson([
            'ok' => true,
            'status' => 'sent',
        ]);
        $this->assertSame(0, Contact::count());

        Mail::assertSent(ContactMail::class, function (ContactMail $mail) {
            return $mail->hasTo('nv09.mkt@datyso.vn');
        });
    }

    public function test_mail_tracking_records_log_mode_and_admin_can_resend_email(): void
    {
        System::updateOrInsert(['keyword' => 'lead_sales_email'], ['content' => 'nv09.mkt@datyso.vn']);
        System::updateOrInsert(['keyword' => 'mail_mailer'], ['content' => 'log']);

        /** @var LeadService $leadService */
        $leadService = app(LeadService::class);
        $ts = time() - 5;

        // Không dùng Mail::fake() để kiểm tra MailManager chạy thật ở chế độ log
        $res = $this->postJson(route('contact.register'), [
            'username' => 'Trương Quang Huy',
            'phone' => '0911223344',
            'email' => 'khachhang@example.com',
            'province' => 'Hà Nội',
            'source_type' => 'contact',
            'hp_company_fax' => '',
            '_form_ts' => (string) $ts,
            '_form_token' => $leadService->makeFormToken($ts),
        ]);

        $res->assertOk()->assertJson(['status' => true]);
        $contact = Contact::where('phone', '0911223344')->firstOrFail();
        $this->assertSame('logged', $contact->mail_status);
        $this->assertStringContainsString('MAIL_MAILER=log', (string) $contact->mail_error);

        // Chuyển sang SMTP với Mail::fake() và Admin bấm gửi lại mail
        System::updateOrInsert(['keyword' => 'mail_mailer'], ['content' => 'smtp']);
        System::updateOrInsert(['keyword' => 'mail_host'], ['content' => 'mail.datyso.com']);

        Mail::fake();
        $admin = User::first();
        $resendRes = $this->actingAs($admin, 'admin')
            ->post(route('admin.contact.resendMail', ['id' => $contact->id]));

        $resendRes->assertRedirect(route('admin.contact.index'));
        $resendRes->assertSessionHas('messageSuccess');

        $contact->refresh();
        $this->assertSame('sent', $contact->mail_status);
        $this->assertNull($contact->mail_error);
        $this->assertNotNull($contact->mail_sent_at);

        Mail::assertSent(ContactMail::class, function (ContactMail $mail) {
            return $mail->hasTo('nv09.mkt@datyso.vn')
                && $mail->hasReplyTo('khachhang@example.com');
        });
    }
}
