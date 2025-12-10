<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffRole;
use App\Models\Staff;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\StaffCredentials;

class StaffController extends Controller
{
    /**
     * Toggle staff active/inactive status
     */
    public function toggleStatus($id)
    {
        try {
            $staff = Staff::findOrFail($id);

            // Toggle status
            $staff->status = !$staff->status;
            $staff->save();

            // Also update user account status if exists
            $user = \App\Models\User::where('email', $staff->email)
                       ->orWhere('email', $staff->staff_code . '@staff.local')
                       ->first();

            if ($user) {
                $user->is_active = $staff->status;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Staff status updated successfully',
                'status' => $staff->status,
                'status_text' => $staff->status ? 'Active' : 'Inactive'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update staff status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send login credentials to staff member via email
     */
    public function sendCredentials($id)
    {
        try {
            $staff = Staff::findOrFail($id);

            // Find associated user account
            $user = \App\Models\User::where('email', $staff->email)
                       ->orWhere('email', $staff->staff_code . '@staff.local')
                       ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No user account found for this staff member'
                ], 404);
            }

            // Generate temporary password if needed
            $tempPassword = 'TempPass123!'; // In production, generate secure password

            // Update user password
            $user->password = Hash::make($tempPassword);
            $user->save();

            // Send email with credentials
            Mail::to($staff->email)->send(new StaffCredentials($staff, $user, $tempPassword));

            return response()->json([
                'success' => true,
                'message' => 'Login credentials sent successfully to ' . $staff->email
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send credentials: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Staff Dashboard - Personal dashboard for staff users
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // Get staff member data
        $staff = Staff::where('email', $user->email)->first();

        if (!$staff) {
            // Try to find by staff_code pattern
            $staffCode = str_replace('@staff.local', '', $user->email);
            $staff = Staff::where('staff_code', $staffCode)->first();
        }

        if (!$staff) {
            return redirect()->route('dashboard')->with('error', 'Staff profile not found. Please contact administrator.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Staff Dashboard API endpoint',
                'staff' => $staff
            ]);
        }

        // Get today's attendance
        $today = now()->toDateString();
        $todayAttendance = \App\Models\Attendance::where('staff_id', $staff->id)
            ->whereDate('attendance_date', $today)
            ->first();

        // Get this month's attendance summary
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $monthlyAttendance = \App\Models\Attendance::where('staff_id', $staff->id)
            ->whereBetween('attendance_date', [$monthStart, $monthEnd])
            ->get();

        $presentDays = $monthlyAttendance->where('status', 'present')->count();
        $totalWorkingDays = now()->day;

        // Get current salary info
        $currentSalary = $staff->salary;

        // Get recent activities (last 5 attendance records)
        $recentActivities = \App\Models\Attendance::where('staff_id', $staff->id)
            ->orderBy('attendance_date', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.staff.dashboard.index', compact(
            'staff',
            'todayAttendance',
            'presentDays',
            'totalWorkingDays',
            'currentSalary',
            'recentActivities'
        ));
    }

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

        // ✅ Create user account for staff login
        $userEmail = $validated['email'] ?? ($staff->staff_code . '@staff.local');
        $defaultPassword = 'password'; // Default password - should be changed by staff

        $user = User::create([
            'name' => $validated['full_name'],
            'email' => $userEmail,
            'password' => Hash::make($defaultPassword),
            'role' => 'staff',
            'is_active' => true,
        ]);

        // ✅ Return JSON response with login credentials
        return response()->json([
            'success' => true,
            'message' => 'Staff created successfully! Login credentials generated.',
            'staff' => $staff,
            'login_credentials' => [
                'email' => $userEmail,
                'password' => $defaultPassword,
                'note' => 'Please inform the staff member to change their password after first login.'
            ]
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
            'password' => 'nullable|string|min:8|confirmed',
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

        // Update user password if provided
        if (!empty($validated['password'])) {
            $user = User::where('email', $staff->email)
                       ->orWhere('email', $staff->staff_code . '@staff.local')
                       ->first();

            if ($user) {
                $user->password = Hash::make($validated['password']);
                $user->save();
            }
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

/**
 * Staff Attendance Management
 */
public function attendance(Request $request)
{
    $user = Auth::user();
    $staff = Staff::where('email', $user->email)->first();

    if (!$staff) {
        $staffCode = str_replace('@staff.local', '', $user->email);
        $staff = Staff::where('staff_code', $staffCode)->first();
    }

    if (!$staff) {
        return redirect()->route('dashboard.staff.dashboard')->with('error', 'Staff profile not found');
    }

    // Handle check-in/check-out
    if ($request->isMethod('post')) {
        return $this->handleAttendanceAction($request, $staff);
    }

    // Get attendance history
    $attendances = \App\Models\Attendance::where('staff_id', $staff->id)
        ->orderBy('attendance_date', 'desc')
        ->paginate(15);

    // Today's attendance
    $today = now()->toDateString();
    $todayAttendance = \App\Models\Attendance::where('staff_id', $staff->id)
        ->whereDate('attendance_date', $today)
        ->first();

    return view('dashboard.staff.attendance', compact('staff', 'attendances', 'todayAttendance'));
}

/**
 * Handle attendance check-in/check-out
 */
private function handleAttendanceAction(Request $request, $staff)
{
    $action = $request->input('action');
    $today = now()->toDateString();
    $now = now();

    $attendance = \App\Models\Attendance::firstOrNew([
        'staff_id' => $staff->id,
        'attendance_date' => $today,
    ]);

    if ($action === 'check_in') {
        if ($attendance->check_in_time) {
            return redirect()->back()->with('error', 'Already checked in today');
        }
        $attendance->check_in_time = $now;
        $attendance->status = 'present';
        $message = 'Checked in successfully at ' . $now->format('H:i');
    } elseif ($action === 'check_out') {
        if (!$attendance->check_in_time) {
            return redirect()->back()->with('error', 'Please check in first');
        }
        if ($attendance->check_out_time) {
            return redirect()->back()->with('error', 'Already checked out today');
        }
        $attendance->check_out_time = $now;

        // Calculate working hours
        $checkIn = \Carbon\Carbon::parse($attendance->check_in_time);
        $workingHours = $checkIn->diffInHours($now);
        $attendance->working_hours = $workingHours;

        $message = 'Checked out successfully at ' . $now->format('H:i') . '. Working hours: ' . $workingHours;
    }

    $attendance->save();

    return redirect()->back()->with('success', $message);
}

/**
 * Staff Salary View
 */
public function salary(Request $request)
{
    $user = Auth::user();
    $staff = Staff::where('email', $user->email)->first();

    if (!$staff) {
        $staffCode = str_replace('@staff.local', '', $user->email);
        $staff = Staff::where('staff_code', $staffCode)->first();
    }

    if (!$staff) {
        return redirect()->route('dashboard.staff.dashboard')->with('error', 'Staff profile not found');
    }

    // Get salary history
    $salaries = \App\Models\Salary::where('staff_id', $staff->id)
        ->orderBy('created_at', 'desc')
        ->get();

    // Current active salary
    $currentSalary = $salaries->where('status', 'active')->first();

    return view('dashboard.staff.salary', compact('staff', 'salaries', 'currentSalary'));
}

/**
 * Staff Profile Management
 */
public function profile(Request $request)
{
    $user = Auth::user();
    $staff = Staff::where('email', $user->email)->first();

    if (!$staff) {
        $staffCode = str_replace('@staff.local', '', $user->email);
        $staff = Staff::where('staff_code', $staffCode)->first();
    }

    if (!$staff) {
        return redirect()->route('dashboard.staff.dashboard')->with('error', 'Staff profile not found');
    }

    if ($request->isMethod('post')) {
        // Handle profile update
        $request->validate([
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:15',
        ]);

        $staff->update($request->only(['phone', 'address', 'emergency_contact']));

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    return view('dashboard.staff.profile', compact('staff'));
}
}
