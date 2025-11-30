<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffRole;
use App\Models\Staff;
use App\Models\Salary;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $staff = Staff::with(['role', 'salary'])->paginate(15);
            return response()->json($staff);
        }

        // Existing web code
        $staff = Staff::with(['role', 'salary'])->get();
        $stf = Staff::all();
        $total = $stf->count();
        $activeStaff = $stf->where('status', 1)->count();
        $inactiveStaff = $stf->where('status', 0)->count();
        return view('dashboard.staff.index', compact('staff', 'stf', 'total', 'activeStaff', 'inactiveStaff'));
    }
    
    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            $roles = StaffRole::all();
            return response()->json(['roles' => $roles]);
        }

        // Existing web code
        $roles = StaffRole::all();
        return view('dashboard.staff.create', compact('roles'));
    }
    
   public function store(Request $request)
{
    try {
        // ✅ Combine all validations into one
        $validated = $request->validate([
            'full_name'        => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'email'            => 'nullable|email|max:255|unique:staff,email',
            'role_id'          => 'required|exists:staff_roles,id',
            'joining_date'     => 'required|date',
            'address'          => 'required|string|max:500',
            'shift_start_time' => 'required|string',
            'shift_end_time'   => 'required|string',
            'profile_picture'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_proof'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'base_salary'      => 'required|numeric|min:0', // salary included here
        ]);

        // ✅ Handle file uploads
        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')
                                                   ->store('profiles', 'public');
        }

        if ($request->hasFile('id_proof')) {
            $validated['id_proof'] = $request->file('id_proof')
                                            ->store('id_proofs', 'public');
        }

        // ✅ Create staff
        $staff = Staff::create($validated);

        // ✅ Create salary linked to staff
        Salary::create([
            'staff_id'       => $staff->id,
            'base_salary'    => $validated['base_salary'],
            'amount_paid'    => 0,
            'pending_amount' => $validated['base_salary'],
        ]);

        // ✅ Return JSON response
        return response()->json([
            'success' => true,
            'message' => 'Staff created successfully!',
            'staff' => $staff
        ], 201);
        
    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage(),
            'error' => $e->getMessage()
        ], 500);
    }
}


public function show(Request $request, $id)
{
    $staff = Staff::with(['role', 'salary'])->findOrFail($id);

    if ($request->wantsJson()) {
        return response()->json($staff);
    }

    return view('dashboard.staff.show', compact('staff'));
}

// Edit staff member
public function edit(Request $request, $id){
    $staff = Staff::findOrFail($id);
    $roles = StaffRole::all();
    if ($request->wantsJson()) {
        return response()->json([
            'staff' => $staff,
            'roles' => $roles
        ]);
    }
    return view('dashboard.staff.edit', compact('staff', 'roles'));
}

// Update staff member
public function update(Request $request, $id)
{
    try {
        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:staff,email,' . $id,
            'role_id' => 'required|exists:staff_roles,id',
            'joining_date' => 'required|date',
            'address' => 'required|string|max:500',
            'shift_start_time' => 'required|string',
            'shift_end_time' => 'required|string',
            'base_salary' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file uploads
        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        if ($request->hasFile('id_proof')) {
            $validated['id_proof'] = $request->file('id_proof')->store('id_proofs', 'public');
        }

        $staff->update($validated);

        // Update or create salary
        if ($staff->salary) {
            $staff->salary()->update(['base_salary' => $validated['base_salary']]);
        } else {
            Salary::create([
                'staff_id' => $staff->id,
                'base_salary' => $validated['base_salary'],
                'amount_paid' => 0,
                'pending_amount' => $validated['base_salary'],
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Staff updated successfully',
                'staff' => $staff->load('role', 'salary')
            ]);
        }

        return redirect()->route('dashboard.staff.index')->with('success', 'Staff updated successfully.');

    } catch (ValidationException $e) {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        throw $e;
    } catch (\Exception $e) {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
        throw $e;
    }
}

// Delete staff member
public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();
        return response()->json(['success' => true, 'message' => 'Staff member deleted successfully.']);
    }
}
