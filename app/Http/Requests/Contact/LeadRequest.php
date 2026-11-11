<?php

namespace App\Http\Requests\Contact;

use App\Services\LeadService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $demand = $this->input('demand');
        if (($demand === null || $demand === '') && $this->filled('content')) {
            $demand = $this->input('content');
        }

        $this->merge([
            'username' => trim(strip_tags((string) $this->input('username', ''))),
            'phone' => LeadService::normalizePhone($this->input('phone', '')),
            'province' => trim(strip_tags((string) $this->input('province', ''))),
            'demand' => trim(strip_tags((string) ($demand ?? ''))),
            'email' => trim((string) $this->input('email', '')),
            'source_type' => trim((string) $this->input('source_type', 'contact')),
            'source_name' => trim(strip_tags((string) $this->input('source_name', ''))),
            'source_url' => trim((string) $this->input('source_url', '')),
        ]);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^0[235789][0-9]{8,9}$/'],
            'demand' => ['nullable', 'string', 'max:2000'],
            'province' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email:filter', 'max:150'],
            'source_type' => ['nullable', 'string', 'in:product,category,contact'],
            'product_id' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
            'source_name' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Vui lòng nhập họ tên.',
            'username.min' => 'Họ tên cần ít nhất :min ký tự.',
            'username.max' => 'Họ tên không được vượt quá :max ký tự.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại Việt Nam chưa đúng định dạng (VD: 0963535196).',
            'email.email' => 'Địa chỉ email chưa đúng định dạng.',
            'demand.max' => 'Nội dung nhu cầu không được vượt quá :max ký tự.',
            'province.max' => 'Tên Tỉnh/Thành phố không hợp lệ.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // 1. Honeypot chống spam bot tự động điền toàn bộ input
            if (trim((string) $this->input('hp_company_fax', '')) !== '') {
                $validator->errors()->add('spam', 'Yêu cầu không hợp lệ.');
                return;
            }

            /** @var LeadService $leadService */
            $leadService = app(LeadService::class);

            // 2. Kiểm tra thời gian render-to-submit và chữ ký form
            if (!$leadService->verifyFormTimestamp(
                $this->input('_form_ts'),
                $this->input('_form_token')
            )) {
                $validator->errors()->add('spam', 'Phiên biểu mẫu không hợp lệ hoặc thao tác quá nhanh, vui lòng tải lại trang và thử lại.');
                return;
            }

            // 3. Chặn chèn URL spam vào trường họ tên
            $username = (string) $this->input('username', '');
            if (preg_match('/(https?:\/\/|www\.|<a\s)/i', $username)) {
                $validator->errors()->add('username', 'Họ tên không được chứa đường dẫn.');
                return;
            }

            // 4. Google reCAPTCHA v3 (nếu đã cấu hình khóa)
            if (!$validator->errors()->any()) {
                $recaptchaCheck = $leadService->verifyRecaptcha(
                    $this->input('g_recaptcha_response'),
                    $this->ip()
                );
                if (!$recaptchaCheck['ok']) {
                    $validator->errors()->add('spam', $recaptchaCheck['message'] ?? 'Xác thực chống spam thất bại.');
                }
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => $validator->errors()->first() ?: 'Vui lòng kiểm tra lại thông tin.',
                'errors' => $validator->errors(),
            ], 422));
        }

        $redirectUrl = $this->getRedirectUrl();
        if (!str_contains($redirectUrl, '#')) {
            $redirectUrl .= '#lead-quote-section';
        }

        $response = redirect($redirectUrl)
            ->withErrors($validator->errors(), $this->errorBag)
            ->withInput($this->except(['hp_company_fax', '_form_ts', '_form_token', 'g_recaptcha_response']));

        throw new ValidationException($validator, $response);
    }
}
