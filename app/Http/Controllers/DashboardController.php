<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // render dashboard
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            // API response - return dashboard data
            // You can implement aggregation of statistics here
            return response()->json([
                'message' => 'Dashboard API endpoint - implement based on your requirements'
            ]);
        }
        
        // Web response
        return view('dashboard.index');
    }
}
