<?php

use App\Http\Controllers\Api\V1\OdooLeadController;
use App\Http\Middleware\VerifyOdooApiKey;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Odoo CRM / ERP Integration
|--------------------------------------------------------------------------
|
| Tất cả các route tại đây được bảo vệ bằng middleware VerifyOdooApiKey
| Xác thực qua header:
|   Authorization: Bearer <API_KEY>
| hoặc
|   X-API-Key: <API_KEY>
|
*/

Route::prefix('v1')->middleware([VerifyOdooApiKey::class])->group(function () {
    // 1. Lấy danh sách lead (hỗ trợ phân trang, lọc since_id, trạng thái, ngày)
    Route::get('/leads', [OdooLeadController::class, 'index'])->name('api.v1.leads.index');

    // 2. Lấy chi tiết 1 lead theo ID
    Route::get('/leads/{id}', [OdooLeadController::class, 'show'])->name('api.v1.leads.show');

    // 3. Đánh dấu đồng bộ hàng loạt sang Odoo (bulk sync)
    Route::post('/leads/mark-synced', [OdooLeadController::class, 'bulkMarkSynced'])->name('api.v1.leads.bulk-mark-synced');

    // 4. Đánh dấu đồng bộ 1 lead theo ID
    Route::post('/leads/{id}/mark-synced', [OdooLeadController::class, 'markSynced'])->name('api.v1.leads.mark-synced');

    // 5. Cập nhật trạng thái xử lý lead từ Odoo về lại CMS
    Route::post('/leads/{id}/status', [OdooLeadController::class, 'updateStatus'])->name('api.v1.leads.status');
});
