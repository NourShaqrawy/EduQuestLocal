<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // 1. التحقق من المصادقة
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'غير مصرح بالوصول. يلزم تسجيل الدخول.'
            ], 401);
        }

        // 2. الحصول على المستخدم
        $user = Auth::guard('sanctum')->user();

        // 3. التحقق من الصلاحية
        if (!$this->checkUserRole($user, $role)) {
            return response()->json([
                'message' => 'ليس لديك الصلاحية للوصول إلى هذا المورد.'
            ], 403);
        }

        return $next($request);
    }

    protected function checkUserRole($user, $requiredRole)
    {
        // إذا كان المستخدم مديراً، فله كل الصلاحيات
        if ($user->role === 'admin') {
            return true;
        }

        // التحقق من الصلاحية المطلوبة
        return $user->role === $requiredRole;
    }
}