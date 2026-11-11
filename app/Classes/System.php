<?php
namespace App\Classes;

class System{

    public function config(){
        
        $data['seo'] = [
            'label' => 'Cấu hình SEO dành cho trang chủ',
            'description' => 'Cài đặt đầy đủ thông tin về SEO của trang chủ website. Bao gồm tiêu đề SEO, Từ Khóa SEO, Mô Tả SEO, Meta images',
            'value' => [
                'meta_title' => ['type' => 'text', 'label' => 'Tiêu đề SEO'],
                'meta_keyword' => ['type' => 'text', 'label' => 'Từ khóa SEO'],
                'meta_description' => ['type' => 'textarea', 'label' => 'Mô tả SEO'],
                'meta_images' => ['type' => 'images', 'label' => 'Ảnh SEO'],
                'google_news' => ['type' => 'text', 'label' => 'Link Google News'],
            ]
        ];

        $data['lead'] = [
            'label' => 'Cấu hình Form Báo giá / Lead & Chống spam',
            'description' => 'Cài đặt Email Sales nhận yêu cầu báo giá, Google Sheet Webhook (Apps Script) và Google reCAPTCHA v3 (tùy chọn).',
            'value' => [
                'sales_email' => ['type' => 'text', 'label' => 'Email Sales nhận lead (nhiều email cách nhau dấu phẩy)'],
                'google_sheet_webhook' => ['type' => 'text', 'label' => 'Google Sheet Webhook URL (tùy chọn)'],
                'recaptcha_site_key' => ['type' => 'text', 'label' => 'Google reCAPTCHA v3 Site Key (tùy chọn)'],
                'recaptcha_secret_key' => ['type' => 'text', 'label' => 'Google reCAPTCHA v3 Secret Key (tùy chọn)'],
            ]
        ];

        return $data;
    }
	
}
