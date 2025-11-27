<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffRole;
use Illuminate\Http\Request;    
class SalaryController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            // API response - return salary data
            $roles = StaffRole::all();
            return response()->json(['roles' => $roles]);
        }

        // Web response
        $roles=StaffRole::all();
        return view('dashboard.staff.salary.index',compact('roles'));
    }

    public function store(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Salary record created successfully'
            ], 201);
        }

        return redirect()->back()->with('success', 'Salary record created successfully');
    }

    public function show(Request $request, $id)
    {
        if ($request->wantsJson()) {
            // Return salary details for specific staff
            return response()->json([
                'message' => 'Salary details API endpoint'
            ]);
        }

        return view('dashboard.staff.salary.show');
    }
}
