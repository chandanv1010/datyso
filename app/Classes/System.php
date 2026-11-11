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
            'label' => 'Cấu hình Form Báo giá / Lead & Chống spam',
            'description' => 'Cài đặt Email Sales nhận yêu cầu báo giá, Google Sheet Webhook (Apps Script) và Google reCAPTCHA v3 (tùy chọn).',
            'value' => [
                'sales_email' => [
                    'type' => 'text',
                    'label' => 'Email Sales nhận lead (nhiều email cách nhau dấu phẩy)',
                    'placeholder' => 'VD: nv09.mkt@datyso.vn, sales@datyso.com',
                ],
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

        $data['mail'] = [
            'label' => 'Cấu hình gửi Email (SMTP / Hosting Mailer)',
            'description' => 'Cấu hình máy chủ gửi mail (ưu tiên hơn file .env) để gửi Lead báo giá về Sales trên cả Local (Laragon/Windows) và Hosting (cPanel/VPS).',
            'value' => [
                'mailer' => [
                    'type' => 'select',
                    'label' => 'Phương thức gửi mail',
                    'default' => 'auto',
                    'option' => [
                        'auto' => 'Tự động (SMTP nếu có -> .env -> Sendmail/PHP mail trên Hosting)',
                        'smtp' => 'SMTP (Gmail / Mail Tên Miền / Microsoft 365 - Dùng được cả Local & Hosting)',
                        'sendmail' => 'Sendmail (Hosting Linux)',
                        'php_mail' => 'Hàm PHP mail() (Hosting shared khóa SMTP ngoài / proc_open)',
                        'log' => 'Chỉ ghi vào laravel.log (dev local, không gửi ra hộp thư thật)',
                    ],
                ],
                'host' => [
                    'type' => 'text',
                    'label' => 'SMTP Host',
                    'placeholder' => 'VD: smtp.gmail.com, mail.datyso.com, smtp.office365.com',
                ],
                'port' => [
                    'type' => 'text',
                    'label' => 'SMTP Port',
                    'placeholder' => 'VD: 587 (TLS), 465 (SSL), 25',
                ],
                'encryption' => [
                    'type' => 'select',
                    'label' => 'Mã hóa kết nối SMTP',
                    'default' => 'tls',
                    'option' => [
                        'tls' => 'TLS / STARTTLS (Port 587 - Khuyên dùng)',
                        'ssl' => 'SSL (Port 465)',
                        'none' => 'Không mã hóa (Port 25 / MailHog local)',
                    ],
                ],
                'username' => [
                    'type' => 'text',
                    'label' => 'Tài khoản / Email đăng nhập SMTP',
                    'placeholder' => 'VD: noreply@datyso.com hoặc Gmail gửi thông báo',
                ],
                'password' => [
                    'type' => 'password',
                    'label' => 'Mật khẩu SMTP / App Password',
                    'placeholder' => 'Nhập mật khẩu email hoặc App Password 16 ký tự',
                ],
                'from_address' => [
                    'type' => 'text',
                    'label' => 'Email hiển thị người gửi (From Address)',
                    'placeholder' => 'VD: noreply@datyso.com (khuyên trùng tên miền web hoặc email SMTP)',
                ],
                'from_name' => [
                    'type' => 'text',
                    'label' => 'Tên hiển thị người gửi (From Name)',
                    'placeholder' => 'VD: DATYSO Website',
                ],
            ],
        ];

        return $data;
    }
}
