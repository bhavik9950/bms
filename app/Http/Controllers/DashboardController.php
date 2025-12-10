<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // render dashboard - redirects based on role
    public function index(Request $request)
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'technical_admin'])) {
            return redirect()->route('dashboard.admin');
        }

        return redirect()->route('dashboard.staff.dashboard');
    }

    // Admin dashboard
    public function admin(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Admin Dashboard API endpoint'
            ]);
        }

        return view('dashboard.index');
    }
}
