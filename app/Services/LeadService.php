<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
     * Lấy cấu hình Form Lead / Google Sheet Webhook / reCAPTCHA v3 từ Admin System hoặc .env.
     *
     * @return array<string, mixed>
     */
    public function getLeadSettings(): array
    {
        $dbSystems = $this->getDbSystems();

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
                    'message' => 'Hệ thống phát hiện dấu hiệu gửi tự động, vui lòng tải lại trang hoặc gọi Hotline 0968945633.',
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
     * Lưu thông tin Lead vào bảng quản trị (m_contact) và đồng bộ Google Sheet (nếu cấu hình).
     * Không gửi email về hộp thư Sales.
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
        $this->syncToGoogleSheet($contact, $settings['google_sheet_webhook']);

        return [
            'contact' => $contact,
            'is_duplicate' => false,
        ];
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
}
