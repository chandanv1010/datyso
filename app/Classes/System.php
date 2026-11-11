<?php
namespace App\Classes;

class System
{
    public function config()
    {
        $data['seo'] = [
            'label' => 'Cấu hình SEO dành cho trang chủ',
            'description' => 'Cài đặt đầy đủ thông tin về SEO của trang chủ website. Bao gồm tiêu đề SEO, Từ Khóa SEO, Mô Tả SEO, Meta images',
            'value' => [
                'meta_title' => ['type' => 'text', 'label' => 'Tiêu đề SEO'],
                'meta_keyword' => ['type' => 'text', 'label' => 'Từ khóa SEO'],
                'meta_description' => ['type' => 'textarea', 'label' => 'Mô tả SEO'],
                'meta_images' => ['type' => 'images', 'label' => 'Ảnh SEO'],
                'google_news' => ['type' => 'text', 'label' => 'Link Google News'],
            ],
        ];

        $data['lead'] = [
            'label' => 'Cấu hình Form Liên hệ / Báo giá & Chống spam',
            'description' => 'Thông tin khách gửi từ form Liên hệ / Báo giá được lưu trực tiếp về trang Quản trị (Danh sách liên hệ). Có thể cấu hình thêm Google Sheet Webhook (Apps Script) và Google reCAPTCHA v3 (tùy chọn).',
            'value' => [
                'google_sheet_webhook' => [
                    'type' => 'text',
                    'label' => 'Google Sheet Webhook URL (tùy chọn)',
                    'placeholder' => 'https://script.google.com/macros/s/.../exec',
                ],
                'recaptcha_site_key' => [
                    'type' => 'text',
                    'label' => 'Google reCAPTCHA v3 Site Key (tùy chọn)',
                ],
                'recaptcha_secret_key' => [
                    'type' => 'text',
                    'label' => 'Google reCAPTCHA v3 Secret Key (tùy chọn)',
                ],
            ],
        ];

        return $data;
    }
}
