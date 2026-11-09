<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanonicalRedirect
{
    /**
     * Handle an incoming request.
     * Enforce single-step 301 redirect to canonical clean URL:
     * - Strips '/public' prefix (preventing duplicate live URLs under /public/)
     * - Strips trailing slash on non-root URLs (e.g. /san-pham/may-han-laser/ -> /san-pham/may-han-laser)
     * - Preserves query string parameters
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only inspect safe read requests (GET, HEAD)
        if (! $request->isMethodSafe()) {
            return $next($request);
        }

        $uri = $request->getRequestUri();
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $queryString = parse_url($uri, PHP_URL_QUERY);

        // Chặn truy cập vào đường dẫn /backup hoặc /backup/ -> trả về 403 Forbidden
        $cleanPathForCheck = trim(preg_replace('#^/public(?:/|$)#i', '/', $path), '/');
        if (preg_match('#^backup(?:/|$)#i', $cleanPathForCheck)) {
            abort(403, 'Access denied.');
        }

        $rawHost = $request->getHost();
        $hasWww = (bool) preg_match('#^www\.#i', $rawHost);
        $hasPublic = (bool) preg_match('#^/public(?:/|$)#i', $path);
        $hasTrailingSlash = (strlen($path) > 1 && str_ends_with($path, '/'));

        // If host has 'www.' or URL contains '/public' or has a trailing slash (not root '/')
        if ($hasWww || $hasPublic || $hasTrailingSlash) {
            // 1. Remove '/public' prefix if present
            $cleanPath = preg_replace('#^/public(?:/|$)#i', '/', $path);

            // 2. Remove trailing slash (keep root '/' intact)
            $cleanPath = rtrim($cleanPath, '/');
            if ($cleanPath === '') {
                $cleanPath = '/';
            }

            // 3. Re-attach query string if any
            $queryStringPart = ($queryString !== null && $queryString !== '') ? '?' . $queryString : '';

            // 4. Construct target canonical URL
            if ($hasWww) {
                $canonicalHost = preg_replace('#^www\.#i', '', $rawHost);
                $targetUrl = 'https://' . $canonicalHost . $cleanPath . $queryStringPart;
            } else {
                $targetUrl = $cleanPath . $queryStringPart;
            }

            // 301 Permanent Redirect in a single step
            return redirect($targetUrl, 301);
        }

        return $next($request);
    }
}

