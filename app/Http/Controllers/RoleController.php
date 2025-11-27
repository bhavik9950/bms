<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffRole;
use Maatwebsite\Excel\Facades\Excel;

class RoleController extends Controller
{
    //

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $roles = StaffRole::paginate(15);
            return response()->json($roles);
        }

        // Web response
        // Fetch all roles (you can implement pagination if needed)
        $roles = StaffRole::all();
        $active=StaffRole::where('status', true)->count();
        $total=StaffRole::count();
        $assigned=StaffRole::sum('assigned');
        $inactive=$total-$active;
        return view('dashboard.roles.index',compact('roles','active','total','inactive','assigned'));
    }

    public function store(Request $request){
        $request->validate([
            'role' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $role = StaffRole::create([
            'role' => $request->role,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json(['success' => true, 'role' => $role]);
    }
    public function update(Request $request, $id){
        $request->validate([
            'role' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $role = StaffRole::find($id);
        if(!$role){
            return response()->json(['error' => 'Role not found'], 404);
        }

        $role->update([
            'role' => $request->role,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json(['success' => true, 'role' => $role]);
    }
    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');
        $rows=Excel::toArray([], $file)[0];// Get the first sheet data
foreach($rows as $index => $row){
    if($index === 0) continue; // Skip header row
    StaffRole::create([
        'role' => $row[0] ?? 'N/A',
        'description' => $row[1] ?? null,
        'status' => isset($row[2]) ? filter_var($row[2], FILTER_VALIDATE_BOOLEAN) : true,
    ]);
    }
    return response()->json(['success' => count($rows)-1]); 
}
public function destroy($id){
    $role = StaffRole::find($id);
    if(!$role){
        return response()->json(['error' => 'Role not found'], 404);
    }
    $role->delete();
    return response()->json(['success' => true]);
}
}