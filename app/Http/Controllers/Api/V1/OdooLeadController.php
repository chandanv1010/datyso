<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller cung cấp REST API chuyên dụng cho Dev Odoo để lấy dữ liệu Lead từ website DATYSO.
 */
class OdooLeadController extends Controller
{
    /**
     * Lấy danh sách Lead hỗ trợ phân trang và bộ lọc linh hoạt (since_id, trạng thái, ngày gửi...).
     */
    public function index(Request $request): JsonResponse
    {
        Contact::ensureLeadSchema();

        $query = Contact::with(['product.categories', 'category']);
        $this->applyFilters($query, $request);

        $limit = (int) $request->input('limit', $request->input('per_page', 20));
        $limit = max(1, min($limit, 100)); // Giới hạn từ 1 đến tối đa 100 bản ghi mỗi trang

        $paginator = $query->paginate($limit);

        $formattedLeads = collect($paginator->items())->map(function (Contact $contact) {
            return $this->formatLeadData($contact);
        })->values()->all();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách lead thành công.',
            'data' => $formattedLeads,
            'pagination' => [
                'total' => $paginator->total(),
                'count' => count($paginator->items()),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'has_more' => $paginator->hasMorePages(),
            ],
            'filters_applied' => [
                'since_id' => $request->filled('since_id') ? (int) $request->input('since_id') : null,
                'is_supported' => $request->filled('is_supported') ? (int) $request->input('is_supported') : null,
                'is_synced' => $request->filled('is_synced') ? (int) $request->input('is_synced') : null,
                'status' => $request->input('status'),
            ],
        ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
    }

    /**
     * Lấy chi tiết một bản ghi Lead theo ID.
     */
    public function show(int|string $id): JsonResponse
    {
        Contact::ensureLeadSchema();

        $contact = Contact::with(['product.categories', 'category'])->find((int) $id);
        if (! $contact) {
            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy lead có ID #{$id}.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin lead thành công.',
            'data' => $this->formatLeadData($contact),
        ], 200);
    }

    /**
     * Đánh dấu 1 lead đã được đồng bộ thành công sang Odoo (cập nhật odoo_synced_at và odoo_lead_id).
     */
    public function markSynced(Request $request, int|string $id): JsonResponse
    {
        Contact::ensureLeadSchema();

        $contact = Contact::find((int) $id);
        if (! $contact) {
            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy lead có ID #{$id}.",
            ], 404);
        }

        try {
            DB::beginTransaction();

            $contact->odoo_synced_at = now();

            if ($request->filled('odoo_lead_id')) {
                $contact->odoo_lead_id = (string) $request->input('odoo_lead_id');
            }

            if ($request->has('is_supported')) {
                $contact->is_supported = (int) $request->input('is_supported');
            }

            $contact->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã đánh dấu lead #{$id} đồng bộ Odoo thành công.",
                'data' => $this->formatLeadData($contact->fresh(['product.categories', 'category'])),
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('markSynced failed: '.$th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật trạng thái đồng bộ: '.$th->getMessage(),
            ], 500);
        }
    }

    /**
     * Đánh dấu hàng loạt lead đã đồng bộ sang Odoo (Batch Sync Acknowledgment).
     */
    public function bulkMarkSynced(Request $request): JsonResponse
    {
        Contact::ensureLeadSchema();

        $leadIds = $request->input('ids', $request->input('lead_ids', []));
        if (! is_array($leadIds) || empty($leadIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp danh sách mảng ids hoặc lead_ids (VD: [1, 2, 3]).',
            ], 422);
        }

        $cleanIds = array_values(array_filter(array_map('intval', $leadIds)));
        if (empty($cleanIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Mảng ids / lead_ids không chứa ID hợp lệ.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updateData = ['odoo_synced_at' => now()];
            if ($request->has('is_supported')) {
                $updateData['is_supported'] = (int) $request->input('is_supported');
            }

            $updatedCount = Contact::whereIn('id', $cleanIds)->update($updateData);

            // Nếu Odoo gửi kèm mapping ID { "123": "ODOO-100", "124": "ODOO-101" }
            $odooLeadIds = $request->input('odoo_lead_ids', []);
            if (is_array($odooLeadIds) && count($odooLeadIds)) {
                foreach ($odooLeadIds as $cmsId => $odooId) {
                    if (in_array((int) $cmsId, $cleanIds, true) && ! empty($odooId)) {
                        Contact::where('id', (int) $cmsId)->update(['odoo_lead_id' => (string) $odooId]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã đánh dấu đồng bộ {$updatedCount} lead sang Odoo.",
                'synced_count' => $updatedCount,
                'updated_count' => $updatedCount,
                'lead_ids' => $cleanIds,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('bulkMarkSynced failed: '.$th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật trạng thái đồng bộ hàng loạt: '.$th->getMessage(),
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái xử lý (is_supported / is_read / notes) từ Odoo về lại CMS.
     */
    public function updateStatus(Request $request, int|string $id): JsonResponse
    {
        Contact::ensureLeadSchema();

        $contact = Contact::find((int) $id);
        if (! $contact) {
            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy lead có ID #{$id}.",
            ], 404);
        }

        try {
            DB::beginTransaction();

            if ($request->has('is_supported')) {
                $contact->is_supported = (int) $request->input('is_supported');
            }

            if ($request->has('is_read')) {
                $contact->is_read = (int) $request->input('is_read');
            }

            if ($request->filled('notes')) {
                $contact->content = (string) $request->input('notes');
            }

            if ($request->filled('odoo_lead_id')) {
                $contact->odoo_lead_id = (string) $request->input('odoo_lead_id');
            }

            $contact->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Cập nhật trạng thái lead #{$id} thành công.",
                'data' => $this->formatLeadData($contact->fresh(['product.categories', 'category'])),
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('updateStatus failed: '.$th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật trạng thái: '.$th->getMessage(),
            ], 500);
        }
    }

    /**
     * Áp dụng các bộ lọc truy vấn tìm kiếm dữ liệu Lead.
     */
    private function applyFilters(Builder $query, Request $request): void
    {
        // 1. Lọc theo since_id (hỗ trợ lấy tuần tự các bản ghi mới cho Odoo cron)
        if ($request->filled('since_id')) {
            $query->where('id', '>', (int) $request->input('since_id'));
        }

        // 2. Lọc theo trạng thái xử lý trên CMS (0: chưa xử lý, 1: đã xử lý)
        if ($request->filled('is_supported')) {
            $query->where('is_supported', (int) $request->input('is_supported'));
        }

        // 3. Lọc theo trạng thái đã đồng bộ sang Odoo chưa (0: chưa đồng bộ, 1: đã đồng bộ)
        if ($request->filled('is_synced')) {
            $isSynced = (int) $request->input('is_synced');
            if ($isSynced === 1) {
                $query->whereNotNull('odoo_synced_at');
            } elseif ($isSynced === 0) {
                $query->whereNull('odoo_synced_at');
            }
        }

        // 4. Lọc theo trạng thái xem (unread: chưa đọc, read: đã đọc)
        if ($request->filled('status')) {
            $status = strtolower(trim((string) $request->input('status')));
            if ($status === 'unread') {
                $query->where('is_read', 0);
            } elseif ($status === 'read') {
                $query->where('is_read', 1);
            }
        }

        // 5. Lọc theo khoảng ngày tạo
        if ($request->filled('from_date')) {
            try {
                $query->where('created_at', '>=', Carbon::parse($request->input('from_date'))->startOfDay());
            } catch (\Throwable $e) {
                // Bỏ qua nếu định dạng ngày không hợp lệ
            }
        }
        if ($request->filled('to_date')) {
            try {
                $query->where('created_at', '<=', Carbon::parse($request->input('to_date'))->endOfDay());
            } catch (\Throwable $e) {
                // Bỏ qua nếu định dạng ngày không hợp lệ
            }
        }

        // 6. Lọc theo loại nguồn (product | category | contact)
        if ($request->filled('source_type')) {
            $query->where('source_type', trim((string) $request->input('source_type')));
        }

        // 7. Sắp xếp: nếu có since_id mặc định sắp xếp ASC để Odoo đọc theo thứ tự tạo
        $sort = strtolower(trim((string) $request->input('sort', '')));
        if ($sort === 'asc' || ($sort === '' && $request->filled('since_id'))) {
            $query->orderBy('id', 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }
    }

    /**
     * Chuẩn hóa cấu trúc dữ liệu Lead để Dev Odoo map trực tiếp vào model crm.lead.
     *
     * @return array<string, mixed>
     */
    private function formatLeadData(Contact $contact): array
    {
        $product = $contact->product;
        $category = $contact->category ?: ($product?->categories?->first());

        $productData = null;
        if ($product) {
            $productPrice = (float) ($product->price_discount ?: ($product->price ?: 0));
            $productCategorySlug = $product->categories->first()?->slug ?? 'san-pham';

            $productData = [
                'id' => (int) $product->id,
                'name' => (string) $product->name,
                'slug' => (string) $product->slug,
                'price' => $productPrice,
                'url' => route('product.detail', [
                    'categorySlug' => $productCategorySlug,
                    'productSlug' => $product->slug,
                ]),
            ];
        }

        $categoryData = null;
        if ($category) {
            $categoryData = [
                'id' => (int) $category->id,
                'name' => (string) $category->name,
                'slug' => (string) $category->slug,
                'url' => route('product.list', ['categorySlug' => $category->slug]),
            ];
        }

        return [
            'id' => (int) $contact->id,
            'customer_name' => (string) $contact->username,
            'phone' => (string) $contact->phone,
            'email' => $contact->email ?: null,
            'province' => $contact->province ?: null,
            'demand' => $contact->demand ?: null,
            'notes' => $contact->content ?: null,
            'source_type' => (string) ($contact->source_type ?: 'contact'),
            'source_name' => $contact->source_name ?: null,
            'source_url' => $contact->source_url ?: null,
            'customer' => [
                'name' => (string) $contact->username,
                'phone' => (string) $contact->phone,
                'email' => $contact->email ?: null,
                'province' => $contact->province ?: null,
            ],
            'interest' => [
                'source_type' => (string) ($contact->source_type ?: 'contact'),
                'source_name' => $contact->source_name ?: null,
                'source_url' => $contact->source_url ?: null,
                'demand' => $contact->demand ?: null,
            ],
            'product' => $productData,
            'category' => $categoryData,
            'status' => [
                'is_read' => (int) $contact->is_read,
                'is_supported' => (int) $contact->is_supported,
            ],
            'odoo_sync' => [
                'synced' => ! empty($contact->odoo_synced_at),
                'synced_at' => $contact->odoo_synced_at ? Carbon::parse($contact->odoo_synced_at)->format('Y-m-d H:i:s') : null,
                'odoo_lead_id' => $contact->odoo_lead_id ?: null,
            ],
            'odoo_lead_mapping' => [
                'name' => 'Lead Web: '.($contact->source_name ?: 'Tư vấn chung').' - '.$contact->username,
                'contact_name' => (string) $contact->username,
                'phone' => (string) $contact->phone,
                'mobile' => (string) $contact->phone,
                'email_from' => $contact->email ?: null,
                'city' => $contact->province ?: null,
                'description' => ($contact->content ?: $contact->demand) ?: 'Khách gửi yêu cầu từ website DATYSO',
            ],
            'is_read' => (int) $contact->is_read,
            'is_supported' => (int) $contact->is_supported,
            'is_synced' => ! empty($contact->odoo_synced_at),
            'odoo_synced_at' => $contact->odoo_synced_at ? Carbon::parse($contact->odoo_synced_at)->format('Y-m-d H:i:s') : null,
            'odoo_lead_id' => $contact->odoo_lead_id ?: null,
            'ip_address' => $contact->ip_address ?: null,
            'created_at' => $contact->created_at ? $contact->created_at->format('Y-m-d H:i:s') : null,
            'created_at_iso' => $contact->created_at ? $contact->created_at->toISOString() : null,
            'updated_at' => $contact->updated_at ? $contact->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
