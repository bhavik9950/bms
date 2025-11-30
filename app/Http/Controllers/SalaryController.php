<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\Salary;
use Illuminate\Http\Request;
class SalaryController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            // API response - return salary data
            $salaries = Salary::with('staff.role')->get();
            return response()->json(['salaries' => $salaries]);
        }

        // Web response
        $salaries = Salary::with('staff.role')->get();
        $roles = StaffRole::all();
        return view('dashboard.staff.salary.index', compact('salaries', 'roles'));
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
        $staff = Staff::with('role')->findOrFail($id);
        $salary = Salary::where('staff_id', $id)->first();

        if ($request->wantsJson()) {
            // Return salary details for specific staff
            return response()->json([
                'staff' => $staff,
                'salary' => $salary
            ]);
        }

        return view('dashboard.staff.salary.view', compact('staff', 'salary'));
    }
}
