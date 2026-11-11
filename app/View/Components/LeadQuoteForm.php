<?php

namespace App\View\Components;

use App\Services\LeadService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LeadQuoteForm extends Component
{
    public string $sourceType;
    public ?int $productId;
    public ?int $categoryId;
    public string $sourceName;
    public string $sourceUrl;
    public string $title;
    public string $subtitle;
    public bool $showEmail;
    public bool $showSidebar;
    public float $value;
    public string $anchorId;

    public function __construct(
        string $sourceType = 'contact',
        ?int $productId = null,
        ?int $categoryId = null,
        string $sourceName = '',
        string $sourceUrl = '',
        string $title = 'Nhận Báo Giá & Tư Vấn Kỹ Thuật Nhanh',
        string $subtitle = 'Để lại thông tin để kỹ sư DATYSO tư vấn cấu hình máy phù hợp và gửi báo giá chi tiết.',
        bool $showEmail = false,
        bool $showSidebar = true,
        float $value = 0,
        string $anchorId = 'lead-quote-section'
    ) {
        $this->sourceType = in_array($sourceType, ['product', 'category', 'contact'], true) ? $sourceType : 'contact';
        $this->productId = $productId;
        $this->categoryId = $categoryId;
        $this->sourceName = trim($sourceName);
        $this->sourceUrl = trim($sourceUrl);
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->showEmail = $showEmail;
        $this->showSidebar = $showSidebar;
        $this->value = $value;
        $this->anchorId = $anchorId;
    }

    public function render(): View|Closure|string
    {
        /** @var LeadService $leadService */
        $leadService = app(LeadService::class);
        $settings = $leadService->getLeadSettings();
        $formTs = time();

        return view('components.lead-quote-form', [
            'provinces' => $leadService->getProvinces(),
            'formTs' => $formTs,
            'formToken' => $leadService->makeFormToken($formTs),
            'recaptchaEnabled' => $settings['recaptcha_enabled'],
            'recaptchaSiteKey' => $settings['recaptcha_site_key'],
            'resolvedSourceUrl' => $this->sourceUrl !== '' ? $this->sourceUrl : url()->current(),
        ]);
    }
}
