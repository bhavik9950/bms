# Laravel Backend for Web and APK (Android App) Integration Guide

## Overview
Your current Laravel backend is primarily designed for web applications, returning Blade views. To support both web and APK (Android app), we need to adapt it for API-first architecture using Laravel Sanctum for authentication. This guide will walk you through the necessary changes.

## Current Code Analysis
- **Strengths**: Well-structured Laravel project with models, migrations, and controllers.
- **Issues for APK**:
  - Controllers return views instead of JSON responses.
  - No API routes defined beyond the default Sanctum user route.
  - Sanctum not installed.
  - Incomplete controller methods (e.g., `OrderController@store` has no logic).
- **Overall**: Code is functional for web but needs API adaptations. No critical issues preventing APK integration.

## Step 1: Install Laravel Sanctum

Laravel Sanctum provides a simple authentication system for SPAs, mobile applications, and simple APIs.

```bash
composer require laravel/sanctum
```

After installation, publish the Sanctum configuration and migration files:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

## Step 2: Configure Sanctum

### Update API Configuration
In `config/auth.php`, ensure the API guard uses Sanctum:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'sanctum',
        'provider' => null,
    ],
],
```

### Add Sanctum Middleware
In `app/Http/Kernel.php` or `bootstrap/app.php` (Laravel 11), add Sanctum middleware:

```php
// In bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
})
```

### Update User Model
In `app/Models/User.php`, add the `HasApiTokens` trait:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // ... rest of the class
}
```

## Step 3: Modify Controllers for API Support

Update controllers to detect request type and return appropriate responses. Use `request()->wantsJson()` or check for API routes.

### Files to Modify:
- `app/Http/Controllers/OrderController.php`
- `app/Http/Controllers/StaffController.php` (partially done - needs index/create for API)
- `app/Http/Controllers/FabricController.php` (partially done - needs index for API)
- `app/Http/Controllers/MasterController.php`
- `app/Http/Controllers/AttendanceController.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/RelationController.php`
- `app/Http/Controllers/RoleController.php`
- `app/Http/Controllers/SalaryController.php`

### Key Changes Needed:
1. Add `Request $request` parameter to methods that need it.
2. Use `if ($request->wantsJson())` to differentiate API vs web responses.
3. For API responses, return JSON with appropriate HTTP status codes.
4. For web responses, keep existing view returns.
5. Add proper validation and error handling for API requests.

### Detailed Examples:

#### OrderController.php
Current state: Returns views, incomplete store method.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Garment;
use App\Models\Fabric;
use App\Models\Customer;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            // API response
            $orders = Order::with(['customer', 'items.garment', 'items.fabric'])->paginate(15);
            return response()->json($orders);
        }

        // Web response
        return view('dashboard.orders.index');
    }

    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            // API response - return necessary data
            $garments = Garment::all();
            $fabrics = Fabric::all();
            $customers = Customer::all();
            return response()->json([
                'garments' => $garments,
                'fabrics' => $fabrics,
                'customers' => $customers
            ]);
        }

        // Web response
        $garments = Garment::all();
        $fabrics = Fabric::all();
        return view('dashboard.orders.create', compact('garments', 'fabrics'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'status' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.garment_id' => 'required|exists:garments,id',
            'items.*.fabric_id' => 'required|exists:fabrics,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $order = Order::create([
            'customer_id' => $validated['customer_id'],
            'order_date' => $validated['order_date'],
            'status' => $validated['status'],
        ]);

        foreach ($validated['items'] as $item) {
            $order->items()->create($item);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order' => $order->load('items')
            ], 201);
        }

        return redirect()->route('dashboard.orders.index')->with('success', 'Order created successfully.');
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['customer', 'items.garment', 'items.fabric'])->findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json($order);
        }

        return view('dashboard.orders.show', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'status' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.garment_id' => 'required|exists:garments,id',
            'items.*.fabric_id' => 'required|exists:fabrics,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $order->update($validated);
        $order->items()->delete(); // Remove existing items
        foreach ($validated['items'] as $item) {
            $order->items()->create($item);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'order' => $order->load('items')
            ]);
        }

        return redirect()->route('dashboard.orders.index')->with('success', 'Order updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        }

        return redirect()->route('dashboard.orders.index')->with('success', 'Order deleted successfully.');
    }
}
```

#### StaffController.php
Current state: index/create return views, store/destroy return JSON.

Add API support to index and create methods:

```php
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

public function show(Request $request, $id)
{
    $staff = Staff::with(['role', 'salary'])->findOrFail($id);

    if ($request->wantsJson()) {
        return response()->json($staff);
    }

    return view('dashboard.staff.show', compact('staff'));
}

public function update(Request $request, $id)
{
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

    if ($request->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Staff updated successfully',
            'staff' => $staff->load('role', 'salary')
        ]);
    }

    return redirect()->route('dashboard.staff.index')->with('success', 'Staff updated successfully.');
}
```

#### FabricController.php
Current state: index returns view, other methods return JSON.

Update index method:

```php
public function index(Request $request)
{
    if ($request->wantsJson()) {
        $fabrics = Fabric::paginate(15);
        return response()->json($fabrics);
    }

    // Existing web code
    $fabrics = Fabric::all();
    return view('dashboard.masters.fabrics', compact('fabrics'));
}

public function show(Request $request, $id)
{
    $fabric = Fabric::findOrFail($id);

    if ($request->wantsJson()) {
        return response()->json($fabric);
    }

    return view('dashboard.masters.fabric-detail', compact('fabric'));
}

public function update(Request $request, $id)
{
    $fabric = Fabric::findOrFail($id);

    $validated = $request->validate([
        'fabric' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $fabric->update($validated);

    if ($request->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Fabric updated successfully',
            'fabric' => $fabric
        ]);
    }

    return redirect()->route('dashboard.masters.fabrics')->with('success', 'Fabric updated successfully.');
}
```

### Pattern for Other Controllers:
For each controller, follow this pattern:
1. Add `Request $request` to methods that need API support
2. Wrap API logic in `if ($request->wantsJson())`
3. Return JSON responses with consistent structure: `{'success': true/false, 'message': '...', 'data': ...}`
4. Keep existing web logic for non-API requests
5. Add missing CRUD methods (show, update) if needed
6. Add proper validation for all API endpoints
7. Use pagination for index methods to handle large datasets

## Step 4: Add API Routes

In `routes/api.php`, add your resource routes:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\FabricController;
// Add other controllers

Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Orders
    Route::apiResource('orders', OrderController::class);

    // Staff
    Route::apiResource('staff', StaffController::class);

    // Fabrics
    Route::apiResource('fabrics', FabricController::class);

    // Add routes for other resources as needed
    // Garments, Measurements, Customers, etc.
});

// Authentication routes (if not using web auth for API)
Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum');
```

## Step 5: Handle CORS for Mobile Apps

Install and configure CORS middleware:

```bash
composer require fruitcake/laravel-cors
```

Publish and configure CORS in `config/cors.php`.

## Step 6: Additional Considerations

### Validation and Error Handling
- Use Laravel's validation for API requests.
- Return consistent JSON error responses.

### Pagination
For large datasets, use pagination:

```php
$orders = Order::paginate(15);
return response()->json($orders);
```

### File Uploads
For any file uploads (e.g., measurements import), ensure API handles multipart/form-data.

### Testing
Test your APIs using tools like Postman or Insomnia.

### Security
- Use HTTPS in production.
- Implement rate limiting.
- Validate all inputs.

## Mobile App Integration

In your Android app, use libraries like Retrofit or Volley to make HTTP requests to your API endpoints. Include the Sanctum token in the Authorization header:

```
Authorization: Bearer {token}
```

## Conclusion

Your backend can be successfully adapted for both web and APK use. The key changes involve installing Sanctum, modifying controllers for dual responses, and adding API routes. Start with authentication, then gradually add API endpoints for each resource.