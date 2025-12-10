<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check if user can manage staff
     */
    public static function canManageStaff(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAdminAccess();
    }

    /**
     * Check if user can manage orders
     */
    public static function canManageOrders(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAdminAccess();
    }

    /**
     * Check if user can view reports
     */
    public static function canViewReports(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAdminAccess();
    }

    /**
     * Check if user can manage masters (products/services)
     */
    public static function canManageMasters(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAdminAccess();
    }

    /**
     * Check if staff can mark attendance
     */
    public static function canMarkAttendance(): bool
    {
        $user = Auth::user();
        return $user && $user->isStaff();
    }

    /**
     * Check if staff can view their salary
     */
    public static function canViewOwnSalary(): bool
    {
        $user = Auth::user();
        return $user && $user->isStaff();
    }

    /**
     * Check if user can access specific module
     */
    public static function canAccessModule(string $module): bool
    {
        $user = Auth::user();

        if (!$user) return false;

        $modulePermissions = [
            'dashboard' => true, // All authenticated users
            'orders' => $user->hasAdminAccess(),
            'masters' => $user->hasAdminAccess(),
            'staff' => $user->hasAdminAccess(),
            'attendance' => $user->hasAdminAccess() || $user->isStaff(),
            'salary' => $user->hasAdminAccess() || $user->isStaff(),
            'reports' => $user->hasAdminAccess(),
            'roles' => $user->hasAdminAccess(),
        ];

        return $modulePermissions[$module] ?? false;
    }
}