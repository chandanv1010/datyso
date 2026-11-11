<?php

namespace App\Services;

use App\Mail\ContactMail;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use App\Models\System;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LeadService
{
    public const AUTO_REPLY_MESSAGE = 'Cảm ơn quý khách đã gửi yêu cầu! DATYSO sẽ gọi lại trong giờ làm việc.';

    /**
     * Danh sách 63 tỉnh/thành phố Việt Nam (ưu tiên các trung tâm công nghiệp lớn lên đầu).
     *
     * @return array<int, string>
     */
    public function getProvinces(): array
    {
        return [
            'Hà Nội',
            'TP. Hồ Chí Minh',
            'Hải Phòng',
            'Bình Dương',
            'Đồng Nai',
            'Bắc Ninh',
            'Đà Nẵng',
            'An Giang',
            'Bà Rịa - Vũng Tàu',
            'Bắc Giang',
            'Bắc Kạn',
            'Bạc Liêu',
            'Bến Tre',
            'Bình Định',
            'Bình Phước',
            'Bình Thuận',
            'Cà Mau',
            'Cần Thơ',
            'Cao Bằng',
            'Đắk Lắk',
            'Đắk Nông',
            'Điện Biên',
            'Đồng Tháp',
            'Gia Lai',
            'Hà Giang',
            'Hà Nam',
            'Hà Tĩnh',
            'Hải Dương',
            'Hậu Giang',
            'Hòa Bình',
            'Hưng Yên',
            'Khánh Hòa',
            'Kiên Giang',
            'Kon Tum',
            'Lai Châu',
            'Lâm Đồng',
            'Lạng Sơn',
            'Lào Cai',
            'Long An',
            'Nam Định',
            'Nghệ An',
            'Ninh Bình',
            'Ninh Thuận',
            'Phú Thọ',
            'Phú Yên',
            'Quảng Bình',
            'Quảng Nam',
            'Quảng Ngãi',
            'Quảng Ninh',
            'Quảng Trị',
            'Sóc Trăng',
            'Sơn La',
            'Tây Ninh',
            'Thái Bình',
            'Thái Nguyên',
            'Thanh Hóa',
            'Thừa Thiên Huế',
            'Tiền Giang',
            'Trà Vinh',
            'Tuyên Quang',
            'Vĩnh Long',
            'Vĩnh Phúc',
            'Yên Bái',
        ];
    }

    /**
     * Chuẩn hóa số điện thoại Việt Nam trước khi validate/lưu.
     */
    public static function normalizePhone(?string $phone): string
    {
        $cleaned = preg_replace('/[^\d+]/', '', (string) $phone);
        if ($cleaned === null || $cleaned === '') {
            return '';
        }

        if (str_starts_with($cleaned, '+84')) {
            $cleaned = '0' . substr($cleaned, 3);
        } elseif (str_starts_with($cleaned, '84') && strlen($cleaned) >= 11) {
            $cleaned = '0' . substr($cleaned, 2);
        }

        return ltrim($cleaned, '+');
    }

    /**
     * Tạo chữ ký HMAC cho timestamp hiển thị form (chống bot POST trực tiếp không qua render).
     */
    public function makeFormToken(int $timestamp): string
    {
        $key = (string) (config('app.key') ?: 'datyso-lead-key');
        return hash_hmac('sha256', (string) $timestamp, $key);
    }

    /**
     * Xác thực thời gian từ lúc render form tới lúc submit tối thiểu 1 giây và không quá 7 ngày.
     */
    public function verifyFormTimestamp(?string $rawTimestamp, ?string $token): bool
    {
        if (empty($rawTimestamp) || empty($token) || !ctype_digit((string) $rawTimestamp)) {
            return false;
        }

        $timestamp = (int) $rawTimestamp;
        $expectedToken = $this->makeFormToken($timestamp);
        if (!hash_equals($expectedToken, (string) $token)) {
            return false;
        }

        $elapsed = time() - $timestamp;
        return $elapsed >= 1 && $elapsed <= 604800;
    }

    /**
     * Đọc toàn bộ cấu hình trong bảng systems an toàn.
     *
     * @return array<string, mixed>
     */
    public function getDbSystems(): array
    {
        try {
            if (Schema::hasTable('systems')) {
                return convert_array(System::get(), 'keyword', 'content');
            }
        } catch (\Throwable $e) {
            // An toàn khi bảng systems chưa khởi tạo hoặc đang chạy CLI không có DB
        }

        return [];
    }

    /**
     * Lấy cấu hình nhận Lead / Email Sales / Google Sheet / reCAPTCHA v3 từ Admin System hoặc .env.
     *
     * @return array<string, mixed>
     */
    public function getLeadSettings(): array
    {
        $dbSystems = $this->getDbSystems();

        $rawSalesEmail = trim((string) ($dbSystems['lead_sales_email'] ?? ''));
        if ($rawSalesEmail === '') {
            $rawSalesEmail = trim((string) config('services.lead.sales_email', ''));
        }

        $salesEmails = $this->parseEmails($rawSalesEmail);

        $sheetWebhook = trim((string) ($dbSystems['lead_google_sheet_webhook'] ?? ''));
        if ($sheetWebhook === '') {
            $sheetWebhook = trim((string) config('services.lead.google_sheet_webhook', ''));
        }

        $siteKey = trim((string) ($dbSystems['lead_recaptcha_site_key'] ?? ''));
        if ($siteKey === '') {
            $siteKey = trim((string) config('services.lead.recaptcha_site_key', ''));
        }

        $secretKey = trim((string) ($dbSystems['lead_recaptcha_secret_key'] ?? ''));
        if ($secretKey === '') {
            $secretKey = trim((string) config('services.lead.recaptcha_secret_key', ''));
        }

        return [
            'sales_emails' => $salesEmails,
            'google_sheet_webhook' => filter_var($sheetWebhook, FILTER_VALIDATE_URL) ? $sheetWebhook : '',
            'recaptcha_site_key' => $siteKey,
            'recaptcha_secret_key' => $secretKey,
            'recaptcha_enabled' => ($siteKey !== '' && $secretKey !== ''),
            'recaptcha_min_score' => (float) config('services.lead.recaptcha_min_score', 0.5),
        ];
    }

    /**
     * Xác thực Google reCAPTCHA v3 nếu người quản trị đã cấu hình cặp khóa Site/Secret Key.
     *
     * @return array{ok: bool, skipped?: bool, message?: string}
     */
    public function verifyRecaptcha(?string $token, ?string $ip = null): array
    {
        $settings = $this->getLeadSettings();
        if (!$settings['recaptcha_enabled']) {
            return ['ok' => true, 'skipped' => true];
        }

        if (empty($token)) {
            return [
                'ok' => false,
                'message' => 'Xác thực bảo mật chưa hoàn tất, vui lòng thử lại sau giây lát.',
            ];
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $settings['recaptcha_secret_key'],
                    'response' => $token,
                    'remoteip' => $ip,
                ]);

            if (!$response->successful()) {
                Log::warning('reCAPTCHA HTTP error', ['status' => $response->status()]);
                return [
                    'ok' => false,
                    'message' => 'Không thể xác thực chống spam lúc này, vui lòng thử lại.',
                ];
            }

            $body = $response->json();
            $success = (bool) ($body['success'] ?? false);
            $score = (float) ($body['score'] ?? 0);
            $action = (string) ($body['action'] ?? '');

            if (!$success || $score < $settings['recaptcha_min_score'] || ($action !== '' && $action !== 'generate_lead')) {
                Log::info('Lead blocked by reCAPTCHA v3', [
                    'success' => $success,
                    'score' => $score,
                    'action' => $action,
                    'ip' => $ip,
                ]);
                return [
                    'ok' => false,
                    'message' => 'Hệ thống phát hiện dấu hiệu gửi tự động, vui lòng tải lại trang hoặc gọi Hotline 0963.535.196.',
                ];
            }

            return ['ok' => true];
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA verify exception: ' . $e->getMessage());
            // Ở môi trường local không có internet hoặc firewall chặn google.com, tránh khóa nhầm test
            if (app()->environment('local')) {
                return ['ok' => true, 'skipped' => true];
            }
            return [
                'ok' => false,
                'message' => 'Lỗi kết nối xác thực bảo mật, vui lòng thử lại.',
            ];
        }
    }

    /**
     * Lưu thông tin Lead vào bảng quản trị, gửi Email Sales và đồng bộ Google Sheet (nếu cấu hình).
     *
     * @param  array<string, mixed>  $validated
     * @return array{contact: Contact, is_duplicate: bool}
     */
    public function createLead(array $validated, Request $request): array
    {
        Contact::ensureLeadSchema();

        $sourceType = in_array(($validated['source_type'] ?? ''), ['product', 'category', 'contact'], true)
            ? $validated['source_type']
            : 'contact';

        $product = !empty($validated['product_id']) ? Product::find((int) $validated['product_id']) : null;
        $category = !empty($validated['category_id']) ? Category::find((int) $validated['category_id']) : null;

        if ($product && !$category) {
            $category = $product->categories()->first();
        }

        $sourceName = trim((string) ($validated['source_name'] ?? ''));
        if ($product) {
            $sourceName = $product->name;
            if ($sourceType === 'contact') {
                $sourceType = 'product';
            }
        } elseif ($category && $sourceName === '') {
            $sourceName = $category->name;
            if ($sourceType === 'contact') {
                $sourceType = 'category';
            }
        }

        $sourceUrl = Str::limit(trim((string) ($validated['source_url'] ?? $request->headers->get('referer', ''))), 500, '');
        $province = trim((string) ($validated['province'] ?? ''));
        $demand = trim((string) ($validated['demand'] ?? ($validated['content'] ?? '')));
        $email = trim((string) ($validated['email'] ?? ''));
        $phone = self::normalizePhone($validated['phone'] ?? '');
        $username = trim((string) ($validated['username'] ?? ''));

        // Chống double-click gửi trùng trong vòng 45 giây cho cùng SĐT + Nguồn xem
        $recentDuplicate = Contact::where('phone', $phone)
            ->where('source_name', $sourceName !== '' ? $sourceName : null)
            ->where('created_at', '>=', now()->subSeconds(45))
            ->orderByDesc('id')
            ->first();

        if ($recentDuplicate) {
            return [
                'contact' => $recentDuplicate,
                'is_duplicate' => true,
            ];
        }

        $summaryContent = $this->buildSummaryContent([
            'source_type' => $sourceType,
            'source_name' => $sourceName,
            'province' => $province,
            'demand' => $demand,
        ]);

        $contact = DB::transaction(function () use (
            $username,
            $email,
            $phone,
            $province,
            $demand,
            $summaryContent,
            $product,
            $category,
            $sourceType,
            $sourceName,
            $sourceUrl,
            $request
        ) {
            return Contact::create([
                'username' => $username,
                'email' => $email !== '' ? $email : null,
                'phone' => $phone,
                'province' => $province !== '' ? $province : null,
                'demand' => $demand !== '' ? $demand : null,
                'content' => $summaryContent,
                'product_id' => $product?->id,
                'category_id' => $category?->id,
                'source_type' => $sourceType,
                'source_name' => $sourceName !== '' ? Str::limit($sourceName, 255, '') : null,
                'source_url' => $sourceUrl !== '' ? $sourceUrl : null,
                'ip_address' => $request->ip(),
            ]);
        });

        $contact->load(['product', 'category']);

        $settings = $this->getLeadSettings();
        $this->notifySales($contact, $settings['sales_emails']);
        $this->syncToGoogleSheet($contact, $settings['google_sheet_webhook']);

        return [
            'contact' => $contact,
            'is_duplicate' => false,
        ];
    }

    /**
     * Gửi email thông báo lead mới về cho Sales (lưu trạng thái gửi và bọc try/catch để không chặn trải nghiệm khách).
     *
     * @param  array<int, string>  $recipients
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function notifySales(Contact $contact, array $recipients, array $overrides = []): array
    {
        if ($contact->exists) {
            Contact::ensureLeadSchema();
        }

        if (empty($recipients)) {
            $reason = 'Chưa cấu hình Email Sales nhận lead trong Admin > Cấu hình hệ thống.';
            Log::info('Lead #' . ($contact->id ?: 'test') . ' saved without Sales email because no recipient is configured.');
            $this->persistMailTracking($contact, 'skipped', $reason);

            return [
                'ok' => false,
                'status' => 'skipped',
                'mailer' => 'none',
                'from' => '',
                'recipients' => [],
                'host' => '',
                'port' => null,
                'error' => $reason,
            ];
        }

        $mailConfig = $this->resolveMailRuntimeConfig($overrides);
        $mailable = new ContactMail($contact, $mailConfig['from_address'], $mailConfig['from_name']);
        $delivery = $this->deliverMailable($recipients, $mailable, $mailConfig);

        $this->persistMailTracking($contact, $delivery['status'], $delivery['error']);

        return [
            'ok' => $delivery['status'] === 'sent',
            'status' => $delivery['status'],
            'mailer' => $delivery['mailer'],
            'from' => $mailConfig['from_address'],
            'recipients' => $recipients,
            'host' => $mailConfig['host'],
            'port' => $mailConfig['port'],
            'error' => $delivery['error'],
        ];
    }

    /**
     * Tổng hợp cấu hình gửi email hiệu lực từ Admin System + .env (tự fix SSL 465 & From Email).
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function resolveMailRuntimeConfig(array $overrides = []): array
    {
        $dbSystems = $this->getDbSystems();

        $pick = static function (string $key) use ($overrides, $dbSystems): string {
            if (array_key_exists($key, $overrides) && $overrides[$key] !== null) {
                return trim((string) $overrides[$key]);
            }
            return trim((string) ($dbSystems[$key] ?? ''));
        };

        $selectedMailer = strtolower($pick('mail_mailer') ?: 'auto');
        $dbHost = $pick('mail_host');
        $dbPort = $pick('mail_port');
        $dbEnc = strtolower($pick('mail_encryption'));
        $dbUser = $pick('mail_username');
        $dbFrom = $pick('mail_from_address');
        $dbFromName = $pick('mail_from_name');

        if (!empty($overrides['clear_mail_password'])) {
            $dbPass = '';
        } elseif (array_key_exists('mail_password', $overrides) && trim((string) $overrides['mail_password']) !== '') {
            $dbPass = (string) $overrides['mail_password'];
        } else {
            $dbPass = $this->decryptSecret($dbSystems['mail_password'] ?? null);
        }

        $envDefault = strtolower(trim((string) config('mail.default', 'log')));
        $envSmtp = (array) config('mail.mailers.smtp', []);
        $envHost = trim((string) ($envSmtp['host'] ?? ''));
        $envPort = trim((string) ($envSmtp['port'] ?? ''));
        $envEnc = strtolower(trim((string) ($envSmtp['encryption'] ?? '')));
        $envUser = trim((string) ($envSmtp['username'] ?? ''));
        $envPass = (string) ($envSmtp['password'] ?? '');
        $envFromAddr = trim((string) config('mail.from.address', ''));
        $envFromName = trim((string) config('mail.from.name', ''));

        $effectiveMailer = $selectedMailer;
        $autoFallbackPhpMail = false;

        $requestHost = '';
        if (app()->bound('request') && request()) {
            $requestHost = strtolower((string) request()->getHost());
        }
        $isPublicHostingLinux = PHP_OS_FAMILY === 'Linux'
            && !app()->runningUnitTests()
            && !app()->environment('testing')
            && (
                app()->environment('production')
                || (
                    $requestHost !== ''
                    && !in_array($requestHost, ['localhost', '127.0.0.1', '::1'], true)
                    && !str_ends_with($requestHost, '.local')
                    && !str_ends_with($requestHost, '.test')
                )
            );

        if ($effectiveMailer === 'auto' || $effectiveMailer === '') {
            if (app()->runningUnitTests() && $envDefault === 'array' && empty($overrides) && $dbHost === '') {
                $effectiveMailer = 'array';
            } elseif ($dbHost !== '') {
                $effectiveMailer = 'smtp';
            } elseif (!in_array($envDefault, ['', 'log', 'array'], true)) {
                $effectiveMailer = $envDefault;
            } elseif ($isPublicHostingLinux) {
                if ($this->canUseSendmail()) {
                    $effectiveMailer = 'sendmail';
                    $autoFallbackPhpMail = function_exists('mail');
                } elseif (function_exists('mail')) {
                    $effectiveMailer = 'php_mail';
                } else {
                    $effectiveMailer = 'log';
                }
            } else {
                $effectiveMailer = 'log';
            }
        }

        $usingDbSmtp = ($dbHost !== '');
        $host = $usingDbSmtp ? $dbHost : ($envHost !== '' && $envDefault === 'smtp' ? $envHost : '');
        if ($host === '' && $effectiveMailer === 'smtp' && $envHost !== '' && $envHost !== '127.0.0.1') {
            $host = $envHost;
        }
        $enc = $usingDbSmtp ? ($dbEnc !== '' ? $dbEnc : 'tls') : $envEnc;
        if (!in_array($enc, ['tls', 'ssl', 'none', ''], true)) {
            $enc = 'tls';
        }
        $portRaw = $usingDbSmtp ? $dbPort : $envPort;
        $port = ($portRaw !== '' && ctype_digit($portRaw))
            ? (int) $portRaw
            : ($enc === 'ssl' ? 465 : ($enc === 'none' ? 25 : 587));
        $user = $usingDbSmtp ? $dbUser : ($dbUser !== '' ? $dbUser : $envUser);
        $pass = $usingDbSmtp ? $dbPass : ($dbPass !== '' ? $dbPass : $envPass);

        $fromAddress = $this->resolveFromAddress($dbFrom, $envFromAddr, $user);
        $fromName = $dbFromName !== ''
            ? $dbFromName
            : (($envFromName !== '' && !in_array($envFromName, ['Example', 'Laravel'], true)) ? $envFromName : 'DATYSO Website');

        return [
            'configured_mailer' => $selectedMailer,
            'mailer' => $effectiveMailer,
            'host' => $host,
            'port' => $port,
            'encryption' => $enc === '' ? 'tls' : $enc,
            'username' => $user,
            'password' => $pass,
            'from_address' => $fromAddress,
            'from_name' => $fromName,
            'sendmail_path' => (string) config('mail.mailers.sendmail.path', '/usr/sbin/sendmail -bs -i'),
            'auto_fallback_php_mail' => $autoFallbackPhpMail,
        ];
    }

    /**
     * Chẩn đoán nhanh trạng thái cấu hình gửi email (hiển thị trong Admin).
     *
     * @return array<string, mixed>
     */
    public function getMailDiagnostics(): array
    {
        $leadSettings = $this->getLeadSettings();
        $mailConfig = $this->resolveMailRuntimeConfig();
        $issues = [];

        if (empty($leadSettings['sales_emails'])) {
            $issues[] = 'Chưa nhập Email Sales nhận lead.';
        }

        if ($mailConfig['mailer'] === 'log') {
            $issues[] = PHP_OS_FAMILY === 'Windows'
                ? 'Đang ở chế độ "log" (máy Local Windows): mail chỉ ghi vào storage/logs/laravel.log. Hãy chọn SMTP (Gmail / Mail Hosting / Microsoft 365) bên dưới để gửi mail thật.'
                : 'Hệ thống đang dùng MAIL_MAILER=log nên không gửi mail ra ngoài. Hãy cấu hình SMTP hoặc chọn Sendmail / PHP mail() bên dưới.';
        } elseif ($mailConfig['mailer'] === 'smtp' && $mailConfig['host'] === '') {
            $issues[] = 'Đã chọn SMTP nhưng chưa nhập SMTP Host.';
        }

        if ($mailConfig['from_address'] === 'hello@example.com') {
            $issues[] = 'Địa chỉ From Email vẫn đang là mặc định hello@example.com, rất dễ bị Microsoft 365 / Gmail từ chối.';
        }

        return [
            'ready' => empty($issues),
            'sales_emails' => $leadSettings['sales_emails'],
            'configured_mailer' => $mailConfig['configured_mailer'],
            'effective_mailer' => $mailConfig['mailer'],
            'host' => $mailConfig['host'],
            'port' => $mailConfig['port'],
            'encryption' => $mailConfig['encryption'],
            'from_address' => $mailConfig['from_address'],
            'from_name' => $mailConfig['from_name'],
            'issues' => $issues,
        ];
    }

    /**
     * Gửi email kiểm tra ngay từ Admin > Cấu hình hệ thống mà không lưu rác vào danh sách Lead.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function sendTestMail(?string $rawRecipient = null, array $overrides = []): array
    {
        $targetRaw = trim((string) $rawRecipient);
        if ($targetRaw === '' && !empty($overrides['lead_sales_email'])) {
            $targetRaw = trim((string) $overrides['lead_sales_email']);
        }

        $recipients = $this->parseEmails($targetRaw);
        if (empty($recipients)) {
            $recipients = $this->getLeadSettings()['sales_emails'];
        }

        if (empty($recipients)) {
            return [
                'ok' => false,
                'status' => 'skipped',
                'message' => 'Vui lòng nhập địa chỉ Email nhận test (hoặc Email Sales nhận lead) hợp lệ.',
            ];
        }

        $testContact = new Contact([
            'username' => 'Kiểm tra gửi Email - DATYSO',
            'phone' => '0963535196',
            'province' => 'Hà Nội',
            'demand' => 'Đây là email kiểm tra kết nối gửi Lead tự động từ Website DATYSO.',
            'source_type' => 'contact',
            'source_name' => 'Kiểm tra cấu hình Email Admin',
            'source_url' => url('/admin/system'),
        ]);
        $testContact->created_at = now();

        $res = $this->notifySales($testContact, $recipients, $overrides);
        $targetStr = implode(', ', $recipients);

        if ($res['status'] === 'sent') {
            $via = $res['mailer'] === 'smtp'
                ? "SMTP ({$res['host']}:{$res['port']})"
                : strtoupper((string) $res['mailer']);
            return [
                'ok' => true,
                'status' => 'sent',
                'mailer' => $res['mailer'],
                'from' => $res['from'],
                'recipients' => $recipients,
                'message' => "Đã gửi email kiểm tra thành công tới {$targetStr} (từ {$res['from']} qua {$via}). Nếu chưa thấy ở Hộp thư đến, vui lòng kiểm tra thêm thư mục Spam/Junk.",
            ];
        }

        if ($res['status'] === 'logged') {
            return [
                'ok' => false,
                'status' => 'logged',
                'mailer' => 'log',
                'from' => $res['from'],
                'recipients' => $recipients,
                'message' => (string) ($res['error'] ?: 'Hệ thống đang dùng MAIL_MAILER=log nên mail chưa được gửi ra ngoài.'),
            ];
        }

        return [
            'ok' => false,
            'status' => $res['status'],
            'mailer' => $res['mailer'],
            'from' => $res['from'],
            'recipients' => $recipients,
            'message' => 'Gửi email thất bại (' . $res['mailer'] . '): ' . ($res['error'] ?: 'Lỗi không xác định.'),
        ];
    }

    /**
     * Thực thi gửi Mailable qua MailManager (kèm tự fallback Hosting & giữ nguyên Mail::fake()/Mockery cho test).
     *
     * @param  array<int, string>  $recipients
     * @param  array<string, mixed>  $mailConfig
     * @return array{status: string, mailer: string, error: ?string}
     */
    private function deliverMailable(array $recipients, ContactMail $mailable, array $mailConfig): array
    {
        $mailRoot = Mail::getFacadeRoot();
        if (!($mailRoot instanceof MailManager) || $mailRoot instanceof \Mockery\MockInterface) {
            try {
                Mail::to($recipients)->send($mailable);
                return ['status' => 'sent', 'mailer' => 'test_double', 'error' => null];
            } catch (\Throwable $e) {
                Log::error('Failed to send Sales lead email: ' . $e->getMessage());
                return ['status' => 'failed', 'mailer' => 'test_double', 'error' => Str::limit($e->getMessage(), 1500, '')];
            }
        }

        $mailer = $mailConfig['mailer'];

        if ($mailer === 'log') {
            try {
                Mail::mailer('log')->to($recipients)->send($mailable);
            } catch (\Throwable $e) {
                Log::warning('Log mailer write error: ' . $e->getMessage());
            }

            return [
                'status' => 'logged',
                'mailer' => 'log',
                'error' => 'Hệ thống đang dùng MAIL_MAILER=log nên email chỉ được ghi vào storage/logs/laravel.log, chưa gửi tới hộp thư Sales. Hãy cấu hình SMTP trong Admin > Cấu hình hệ thống.',
            ];
        }

        if ($mailer === 'smtp' && $mailConfig['host'] === '') {
            $err = 'Chưa cấu hình SMTP Host trong Admin > Cấu hình hệ thống hoặc biến MAIL_HOST trong .env.';
            Log::error('Lead mail failed: ' . $err);
            return ['status' => 'failed', 'mailer' => 'smtp', 'error' => $err];
        }

        if ($mailer === 'sendmail' && PHP_OS_FAMILY === 'Windows') {
            $err = 'Sendmail chỉ khả dụng trên Hosting Linux; trên máy Local Windows vui lòng cấu hình SMTP.';
            Log::error('Lead mail failed: ' . $err);
            return ['status' => 'failed', 'mailer' => 'sendmail', 'error' => $err];
        }

        $previousFrom = config('mail.from');
        try {
            config([
                'mail.from.address' => $mailConfig['from_address'],
                'mail.from.name' => $mailConfig['from_name'],
            ]);

            if ($mailer === 'array') {
                Mail::mailer('array')->to($recipients)->send($mailable);
                return ['status' => 'sent', 'mailer' => 'array', 'error' => null];
            }

            $runtimeDef = $this->buildRuntimeMailerDefinition($mailer, $mailConfig);
            $this->sendThroughRuntimeMailer('lead_runtime', $runtimeDef, $recipients, $mailable);

            return ['status' => 'sent', 'mailer' => $mailer, 'error' => null];
        } catch (\Throwable $e) {
            if ($mailer === 'sendmail' && !empty($mailConfig['auto_fallback_php_mail']) && function_exists('mail')) {
                try {
                    $fallbackDef = $this->buildRuntimeMailerDefinition('php_mail', $mailConfig);
                    $this->sendThroughRuntimeMailer('lead_runtime_fallback', $fallbackDef, $recipients, $mailable);
                    return ['status' => 'sent', 'mailer' => 'php_mail', 'error' => null];
                } catch (\Throwable $fallbackEx) {
                    Log::error('Failed both sendmail and php_mail fallback: ' . $fallbackEx->getMessage());
                    return [
                        'status' => 'failed',
                        'mailer' => 'sendmail+php_mail',
                        'error' => Str::limit($e->getMessage() . ' | Fallback PHP mail(): ' . $fallbackEx->getMessage(), 1500, ''),
                    ];
                }
            }

            Log::error('Failed to send Sales lead email (' . $mailer . '): ' . $e->getMessage());
            return [
                'status' => 'failed',
                'mailer' => $mailer,
                'error' => Str::limit($e->getMessage(), 1500, ''),
            ];
        } finally {
            config(['mail.from' => $previousFrom]);
        }
    }

    /**
     * @param  array<string, mixed>  $mailConfig
     * @return array<string, mixed>
     */
    private function buildRuntimeMailerDefinition(string $mailer, array $mailConfig): array
    {
        if ($mailer === 'php_mail') {
            return ['transport' => 'php_mail'];
        }

        if ($mailer === 'sendmail') {
            return [
                'transport' => 'sendmail',
                'path' => $mailConfig['sendmail_path'],
            ];
        }

        $enc = $mailConfig['encryption'];
        $port = (int) $mailConfig['port'];
        $scheme = ($enc === 'ssl' || $port === 465) ? 'smtps' : 'smtp';

        return [
            'transport' => 'smtp',
            'scheme' => $scheme,
            'host' => $mailConfig['host'],
            'port' => $port,
            'encryption' => $enc === 'none' ? null : $enc,
            'auto_tls' => $enc !== 'none',
            'username' => $mailConfig['username'] !== '' ? $mailConfig['username'] : null,
            'password' => $mailConfig['password'] !== '' ? $mailConfig['password'] : null,
            'timeout' => 15,
        ];
    }

    /**
     * @param  array<string, mixed>  $mailerDef
     * @param  array<int, string>  $recipients
     */
    private function sendThroughRuntimeMailer(string $mailerName, array $mailerDef, array $recipients, ContactMail $mailable): void
    {
        config(["mail.mailers.{$mailerName}" => $mailerDef]);
        Mail::purge($mailerName);
        Mail::mailer($mailerName)->to($recipients)->send($mailable);
    }

    private function resolveFromAddress(string $dbFrom, string $envFrom, string $smtpUser): string
    {
        if ($dbFrom !== '' && filter_var($dbFrom, FILTER_VALIDATE_EMAIL)) {
            return $dbFrom;
        }

        $placeholders = ['hello@example.com', 'example@example.com', 'mail@example.com'];
        if ($envFrom !== '' && !in_array(strtolower($envFrom), $placeholders, true) && filter_var($envFrom, FILTER_VALIDATE_EMAIL)) {
            return $envFrom;
        }

        if ($smtpUser !== '' && filter_var($smtpUser, FILTER_VALIDATE_EMAIL)) {
            return $smtpUser;
        }

        $host = '';
        if (app()->bound('request') && request()) {
            $host = strtolower((string) request()->getHost());
        }
        if ($host === '') {
            $host = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        }

        $host = preg_replace('/^www\./', '', $host);
        if (
            $host !== ''
            && !in_array($host, ['localhost', '127.0.0.1', '::1'], true)
            && !filter_var($host, FILTER_VALIDATE_IP)
            && str_contains($host, '.')
        ) {
            return 'noreply@' . $host;
        }

        return 'noreply@datyso.com';
    }

    private function canUseSendmail(): bool
    {
        if (!function_exists('proc_open')) {
            return false;
        }

        $disabled = array_map('trim', explode(',', strtolower((string) ini_get('disable_functions'))));
        if (in_array('proc_open', $disabled, true)) {
            return false;
        }

        $sendmailBin = strtok((string) config('mail.mailers.sendmail.path', '/usr/sbin/sendmail -bs -i'), ' ');
        return $sendmailBin !== '' && @is_executable($sendmailBin);
    }

    private function decryptSecret(?string $raw): string
    {
        $val = trim((string) $raw);
        if ($val === '') {
            return '';
        }

        try {
            return (string) Crypt::decryptString($val);
        } catch (DecryptException $e) {
            return $val;
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function persistMailTracking(Contact $contact, string $status, ?string $error): void
    {
        if (!$contact->exists) {
            return;
        }

        try {
            $contact->mail_status = $status;
            $contact->mail_error = $error ? Str::limit($error, 2000, '') : null;
            if ($status === 'sent') {
                $contact->mail_sent_at = now();
            }
            $contact->save();
        } catch (\Throwable $e) {
            Log::warning('Could not persist mail tracking for Contact #' . $contact->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Đồng bộ Lead sang Google Sheet Webhook (Apps Script) nếu có cấu hình URL.
     */
    public function syncToGoogleSheet(Contact $contact, string $webhookUrl): void
    {
        if ($webhookUrl === '') {
            return;
        }

        try {
            $response = Http::timeout(4)->post($webhookUrl, [
                'id' => $contact->id,
                'created_at' => optional($contact->created_at)->format('Y-m-d H:i:s'),
                'username' => $contact->username,
                'phone' => $contact->phone,
                'email' => $contact->email ?? '',
                'province' => $contact->province ?? '',
                'demand' => $contact->demand ?? '',
                'source_type' => $contact->source_type ?? 'contact',
                'source_name' => $contact->source_name ?? '',
                'source_url' => $contact->source_url ?? '',
                'ip_address' => $contact->ip_address ?? '',
            ]);

            if (!$response->successful()) {
                Log::warning('Google Sheet webhook returned non-2xx', [
                    'status' => $response->status(),
                    'contact_id' => $contact->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Google Sheet webhook error for Contact #' . $contact->id . ': ' . $e->getMessage());
        }
    }

    /**
     * @param  array<string, string>  $data
     */
    private function buildSummaryContent(array $data): string
    {
        $parts = [];
        if (!empty($data['source_name'])) {
            $label = $data['source_type'] === 'category' ? 'Danh mục quan tâm' : 'Sản phẩm quan tâm';
            $parts[] = $label . ': ' . $data['source_name'];
        }
        if (!empty($data['province'])) {
            $parts[] = 'Tỉnh/thành: ' . $data['province'];
        }
        if (!empty($data['demand'])) {
            $parts[] = 'Nhu cầu: ' . $data['demand'];
        }

        return implode("\n", $parts);
    }

    /**
     * @return array<int, string>
     */
    private function parseEmails(string $raw): array
    {
        if ($raw === '') {
            return [];
        }

        $candidates = preg_split('/[,;\s]+/', $raw) ?: [];
        $valid = [];
        foreach ($candidates as $email) {
            $email = trim($email);
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $valid[] = $email;
            }
        }

        return array_values(array_unique($valid));
    }
}

