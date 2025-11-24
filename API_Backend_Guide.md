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

### Example: OrderController Update

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Garment;
use App\Models\Fabric;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            // API response
            $orders = Order::with(['customer', 'items'])->get();
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
            return response()->json([
                'garments' => $garments,
                'fabrics' => $fabrics
            ]);
        }

        // Web response
        $garments = Garment::all();
        $fabrics = Fabric::all();
        return view('dashboard.orders.create', compact('garments', 'fabrics'));
    }

    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.garment_id' => 'required|exists:garments,id',
            'items.*.fabric_id' => 'required|exists:fabrics,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Create order logic
        $order = Order::create([
            'customer_id' => $validated['customer_id'],
            'order_date' => now(),
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            $order->items()->create($item);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('items')
            ], 201);
        }

        return redirect()->route('dashboard.orders.index')->with('success', 'Order created successfully.');
    }
}
```

Apply similar patterns to other controllers (StaffController, FabricController, etc.).

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