<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) return redirect()->route('admin.login')->with('messageError', 'Bạn cần đăng nhập để có thể thực hiện thao tác.');
        if (Auth::guard('admin')->user()->status == 2){
            Auth::logout();
            return redirect()->route('admin.login')->with('messageError', 'Tài khoản của bạn đã bị vô hiệu hóa.');;
        } 
        return $next($request);
    }
}
