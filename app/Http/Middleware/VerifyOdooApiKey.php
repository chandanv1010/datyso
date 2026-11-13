<?php

namespace App\Http\Middleware;

use App\Services\LeadService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware xác thực API Key bảo mật cho Dev Odoo / CRM Integration.
 * Chấp nhận API Key qua:
 * - Header Authorization: Bearer <API_KEY>
 * - Header X-API-Key: <API_KEY>
 * - Query string ?api_key=<API_KEY> (hỗ trợ test nhanh trên trình duyệt/webhook)
 */
class VerifyOdooApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = LeadService::getOdooApiKey();
        $providedKey = $this->extractApiKey($request);

        if ($providedKey === null || $providedKey === '' || ! hash_equals($expectedKey, $providedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Vui lòng cung cấp API Key hợp lệ trong header Authorization: Bearer <token> hoặc X-API-Key: <token>.',
            ], 401, [
                'WWW-Authenticate' => 'Bearer realm="DATYSO Odoo API"',
                'Content-Type' => 'application/json; charset=utf-8',
            ]);
        }

        return $next($request);
    }

    /**
     * Trích xuất API Key từ các vị trí tiêu chuẩn trong HTTP Request.
     */
    private function extractApiKey(Request $request): ?string
    {
        // 1. Header Authorization: Bearer <token>
        $bearerToken = $request->bearerToken();
        if ($bearerToken !== null && $bearerToken !== '') {
            return trim($bearerToken);
        }

        // 2. Header X-API-Key
        $headerKey = $request->header('X-API-Key');
        if (is_string($headerKey) && trim($headerKey) !== '') {
            return trim($headerKey);
        }

        // 3. Fallback query param api_key
        $queryKey = $request->query('api_key');
        if (is_string($queryKey) && trim($queryKey) !== '') {
            return trim($queryKey);
        }

        return null;
    }
}
