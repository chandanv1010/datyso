<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Blade Component hiển thị đánh giá sao sản phẩm theo dữ liệu thực tế.
 *
 * Quy tắc nghiệp vụ (Ticket L12):
 * - Không hiển thị điểm sao giả lập/cố định (như "4.7") hay chuỗi rỗng "( đánh giá)".
 * - Chỉ render khối đánh giá khi sản phẩm có ít nhất 1 lượt đánh giá thật từ khách hàng
 *   (reviews_count >= 1) và điểm trung bình hợp lệ (rating > 0).
 * - Khi chưa có hệ thống đánh giá hoặc chưa có lượt đánh giá nào, component tự động
 *   trả về rỗng thông qua shouldRender() để ẩn hoàn toàn trên trang chi tiết và thẻ sản phẩm.
 */
class ProductRating extends Component
{
    /**
     * Điểm đánh giá trung bình đã chuẩn hóa trong khoảng [0.0, 5.0].
     */
    public float $rating;

    /**
     * Tổng số lượt đánh giá hợp lệ từ khách hàng (>= 0).
     */
    public int $reviewsCount;

    /**
     * Số ngôi sao tô sáng tương ứng (0..5) để phân biệt sao đầy và sao rỗng.
     */
    public int $filledStars;

    /**
     * Có hiển thị điểm số thập phân (ví dụ "4.5") bên cạnh dãy sao hay không.
     * Thường bật ở trang chi tiết sản phẩm, tắt trên card danh sách.
     */
    public bool $showScore;

    /**
     * Class CSS bổ sung cho thẻ bọc ngoài cùng tùy ngữ cảnh (chi tiết hay product card).
     */
    public string $wrapperClass;

    /**
     * Đường dẫn neo/liên kết tùy chọn bọc quanh khối đánh giá (chỉ gắn khi hợp lệ).
     */
    public ?string $href;

    /**
     * Khởi tạo component đánh giá sản phẩm với dữ liệu phòng thủ (defensive casting).
     *
     * @param  object|null  $product  Model Product hoặc object chứa rating và reviews_count.
     * @param  bool  $showScore  Bật/tắt hiển thị số điểm trung bình cạnh sao.
     * @param  string  $wrapperClass  Class CSS cho khối wrapper (ví dụ: "mb-3", "p-3 mb-2 mt-auto").
     * @param  string|null  $href  URL hoặc anchor mục đánh giá nếu trang có khối đánh giá thật.
     */
    public function __construct(
        ?object $product = null,
        bool $showScore = false,
        string $wrapperClass = '',
        ?string $href = null
    ) {
        $rawReviewsCount = $product?->reviews_count ?? 0;
        $rawRating = $product?->rating ?? 0;

        $this->reviewsCount = is_numeric($rawReviewsCount)
            ? max(0, (int) $rawReviewsCount)
            : 0;

        $numericRating = is_numeric($rawRating) ? (float) $rawRating : 0.0;

        // Giới hạn điểm đánh giá trong thang chuẩn 0.0 - 5.0 trước khi hiển thị.
        $this->rating = max(0.0, min(5.0, round($numericRating, 1)));
        $this->filledStars = (int) round($this->rating);
        $this->showScore = $showScore;
        $this->wrapperClass = trim($wrapperClass);
        $this->href = ($href !== null && trim($href) !== '') ? trim($href) : null;
    }

    /**
     * Xác định component có được phép render ra HTML hay không.
     *
     * Trả về false nếu chưa có ít nhất 1 đánh giá thật hoặc điểm đánh giá <= 0,
     * đảm bảo không phát sinh HTML sao giả và không vi phạm quy định dữ liệu đánh giá.
     */
    public function shouldRender(): bool
    {
        return $this->reviewsCount >= 1 && $this->rating > 0.0;
    }

    /**
     * Trả về view hiển thị khối sao đánh giá sản phẩm.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-rating');
    }
}

