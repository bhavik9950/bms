# Guide to Convert Staff Create Blade to Edit Blade

This guide will help you modify `resources/views/dashboard/staff/edit.blade.php` to function as an edit form for staff members, assuming you've copied the content from `create.blade.php`.

## Prerequisites
- Ensure you have a `$staff` variable passed to the view containing the staff member data.
- The controller method should pass the staff data, e.g., `return view('dashboard.staff.edit', compact('staff', 'roles'));`

### Controller Methods
You need to add `edit` and `update` methods to your `StaffController.php`.

#### Edit Method
```php
public function edit(Staff $staff)
{
    $roles = StaffRole::all();
    return view('dashboard.staff.edit', compact('staff', 'roles'));
}
```

#### Update Method
```php
public function update(Request $request, Staff $staff)
{
    $validated = $request->validate([
        'full_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'nullable|email|unique:staff,email,' . $staff->id,
        'role_id' => 'required|exists:staff_roles,id',
        'joining_date' => 'required|date',
        'address' => 'required|string',
        'shift_start_time' => 'required',
        'shift_end_time' => 'required',
        'base_salary' => 'required|numeric|min:0',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'id_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Handle file uploads
    if ($request->hasFile('photo')) {
        // Delete old profile picture if exists
        if ($staff->profile_picture) {
            Storage::delete('public/' . $staff->profile_picture);
        }
        $validated['profile_picture'] = $request->file('photo')->store('staff/photos', 'public');
    }

    if ($request->hasFile('id_proof')) {
        // Delete old ID proof if exists
        if ($staff->id_proof) {
            Storage::delete('public/' . $staff->id_proof);
        }
        $validated['id_proof'] = $request->file('id_proof')->store('staff/id_proofs', 'public');
    }

    $staff->update($validated);

    return redirect()->route('dashboard.staff.index')->with('success', 'Staff member updated successfully');
}
```

#### Routes
Add these routes to your `routes/web.php` or appropriate route file:
```php
Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('dashboard.staff.edit');
Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('dashboard.staff.update');
```

## Step-by-Step Changes

### 1. Update Page Title and Header
Change the header to reflect editing instead of creating.

```php
// Line 8: Change title
<h1 class="text-2xl font-semibold">Edit Staff Member</h1>

// Line 9: Change subtitle
<h5 class="text-xs text-gray-500">Edit Employee Information</h5>
```

### 2. Update Form Action and Method
The form needs to submit to the update route with PUT method.

```php
// Line 28: Change form action and add method spoofing
<form action="{{ route('dashboard.staff.update', $staff->id) }}" method="POST" class="lg:col-span-8 bg-white rounded-lg mb-4" id="staff-info" enctype="multipart/form-data">
    @csrf
    @method('PUT')  // Add this line after @csrf
```

### 3. Populate Form Fields with Existing Data
Add `value` attributes to inputs to show current data. Use `old()` for form validation fallback.

#### Full Name Input (Line 52-54)
```php
<input type="text" id="full_name" name="full_name"
       value="{{ old('full_name', $staff->full_name) }}"
       placeholder="Enter full name" required
       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">
```

#### Phone Input (Line 58-60)
```php
<input type="text" id="phone" name="phone"
       value="{{ old('phone', $staff->phone) }}"
       placeholder="+91 55555-012325" required
       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">
```

#### Email Input (Line 66-68)
```php
<input type="email" id="email" name="email"
       value="{{ old('email', $staff->email) }}"
       placeholder="Enter email address"
       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">
```

#### Joining Date Input (Line 75-77)
```php
<input type="text" id="joining_date" name="joining_date"
       value="{{ old('joining_date', $staff->joining_date ? $staff->joining_date->format('d-m-Y') : '') }}"
       placeholder="DD-MM-YYYY" x-mask="99-99-9999" required
       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2 pr-10">
```

#### Address Input (Line 86-88)
```php
<input type="text" id="address" name="address"
       value="{{ old('address', $staff->address) }}"
       placeholder="Enter staff member address" required
       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">
```

### 4. Update Role Select Dropdown
Make the current role selected.

```php
// Lines 99-104: Update select
<select name="role_id" id="role"
  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2" required>
  @foreach ($roles as $role)
    <option value="{{ $role->id }}" {{ old('role_id', $staff->role_id) == $role->id ? 'selected' : '' }}>{{ $role->role }}</option>
  @endforeach
</select>
```

### 5. Update Shift Time Inputs
Populate shift times.

#### Start Time (Line 110-112)
```php
<input type="time" name="shift_start_time" id="shift_start_time"
       value="{{ old('shift_start_time', $staff->shift_start_time) }}"
       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2"
       required>
```

#### End Time (Line 116-118)
```php
<input type="time" name="shift_end_time" id="shift_end_time"
       value="{{ old('shift_end_time', $staff->shift_end_time) }}"
       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2"
       required>
```

### 6. Update Base Salary Input
Note: Assuming you have a salary relationship or field. Adjust based on your model.

```php
// Line 123-125: Assuming salary is in a related model or field
<input type="text" id="salary" name="base_salary"
       value="{{ old('base_salary', $staff->salary ? $staff->salary->base_salary : '') }}"
       placeholder="Enter salary amount" required
       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 sm:text-sm p-2">
```

### 7. Update Profile Photo Display
Show existing profile photo if available.

```php
// Line 169-170: Update image src
<img id="profilePreview" class="w-20 h-20 p-1 rounded-full ring-2 ring-gray-300 dark:ring-gray-500"
     src="{{ $staff->profile_picture ? asset('storage/' . $staff->profile_picture) : 'https://avatar.iran.liara.run/public' }}" alt="Bordered avatar">
```

### 8. Update Submit Buttons
Change button text and icons to reflect updating.

```php
// Line 146-150: Main submit button
<button type="submit" id="submit-staff"
        class="px-4 py-2 rounded-md bg-indigo-500 hover:bg-indigo-600 text-white flex items-center">
  <i class="ti ti-edit mr-2"></i>
  Update Staff Member
</button>

// Line 189-193: Sidebar submit button
<button type="submit" form="staff-info"
        class="mt-4 w-full px-4 py-2 rounded-md bg-indigo-500 hover:bg-indigo-600 text-white flex items-center justify-center">
  <i class="ti ti-device-floppy mr-2"></i>
  Update
</button>
```

### 9. Handle File Uploads for Existing Files
For ID proof, you might want to show if a file exists and allow replacement.

```php
// After line 143, you can add a section to show existing ID proof
@if($staff->id_proof)
<div class="mt-2">
  <p class="text-sm text-gray-600">Current ID Proof:</p>
  <img src="{{ asset('storage/' . $staff->id_proof) }}" alt="ID Proof" class="max-w-xs max-h-32">
</div>
@endif
```

### 10. Update Staff Code Display
The staff code input is already set to show the existing code, but ensure it's visible.

```php
// Line 38-44: Change to visible input or span
<span class="w-48 rounded-lg shadow-sm sm:text-sm p-2 bg-gray-100 text-right">
  {{ $staff->staff_code }}
</span>
```

## Additional Considerations

1. **Validation**: Ensure your controller validation allows for updates (e.g., unique email except for current staff).

2. **File Handling**: For profile pictures and ID proofs, handle existing files properly in the controller.

3. **JavaScript**: Update any JavaScript that handles form submission or file uploads to work with edit mode.

4. **Routes**: Ensure you have a route like `Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('dashboard.staff.update');`

5. **Controller**: Implement the `edit` and `update` methods in your StaffController.

## Example Controller Methods

```php
public function edit(Staff $staff)
{
    $roles = StaffRole::all();
    return view('dashboard.staff.edit', compact('staff', 'roles'));
}

public function update(Request $request, Staff $staff)
{
    // Validation and update logic
    $validated = $request->validate([
        'full_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'nullable|email|unique:staff,email,' . $staff->id,
        // ... other validations
    ]);

    // Handle file uploads
    if ($request->hasFile('photo')) {
        // Delete old file if exists
        // Upload new file
    }

    $staff->update($validated);

    return redirect()->route('dashboard.staff')->with('success', 'Staff updated successfully');
}
```

This guide covers the main changes needed to convert the create form to an edit form. Test thoroughly after implementation.