# 🎯 Complete Guide: Creating Dual-Purpose Laravel Controllers (Web + API)

## 📋 **Template for New Controllers**

### **1. Basic Controller Structure**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YourModel; // Replace with your model
use Illuminate\Validation\ValidationException;

class YourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            // 🟢 API Response
            $data = YourModel::with(['relations'])->paginate(15);
            return response()->json($data);
        }

        // 🔵 Web Response  
        $data = YourModel::with(['relations'])->get();
        return view('your.index-view', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            // 🟢 API Response - Return necessary data for form
            return response()->json([
                'message' => 'Use POST /your-resource to create',
                'relatedData' => RelatedModel::all()
            ]);
        }

        // 🔵 Web Response
        $relatedData = RelatedModel::all();
        return view('your.create-view', compact('relatedData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                // Your validation rules
                'field' => 'required|string|max:255',
                // ... more fields
            ]);

            $resource = YourModel::create($validated);

            if ($request->wantsJson()) {
                // 🟢 API Response
                return response()->json([
                    'success' => true,
                    'message' => 'Resource created successfully',
                    'data' => $resource->load('relations')
                ], 201);
            }

            // 🔵 Web Response
            return redirect()->route('your.index')
                           ->with('success', 'Resource created successfully');

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

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $resource = YourModel::with(['relations'])->findOrFail($id);

        if ($request->wantsJson()) {
            // 🟢 API Response
            return response()->json($resource);
        }

        // 🔵 Web Response
        return view('your.show-view', compact('resource'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $resource = YourModel::with(['relations'])->findOrFail($id);

        if ($request->wantsJson()) {
            // 🟢 API Response - Return resource and related data
            return response()->json([
                'resource' => $resource,
                'relatedData' => RelatedModel::all()
            ]);
        }

        // 🔵 Web Response
        $relatedData = RelatedModel::all();
        return view('your.edit-view', compact('resource', 'relatedData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $resource = YourModel::findOrFail($id);

            $validated = $request->validate([
                // Your validation rules
                'field' => 'required|string|max:255',
                // ... more fields
            ]);

            $resource->update($validated);

            if ($request->wantsJson()) {
                // 🟢 API Response
                return response()->json([
                    'success' => true,
                    'message' => 'Resource updated successfully',
                    'data' => $resource->load('relations')
                ]);
            }

            // 🔵 Web Response
            return redirect()->route('your.index')
                           ->with('success', 'Resource updated successfully');

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $resource = YourModel::findOrFail($id);
            $resource->delete();

            if ($request->wantsJson()) {
                // 🟢 API Response
                return response()->json([
                    'success' => true,
                    'message' => 'Resource deleted successfully'
                ]);
            }

            // 🔵 Web Response
            return redirect()->route('your.index')
                           ->with('success', 'Resource deleted successfully');

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
}
```

## 🛠️ **Essential Rules to Follow**

### **1. Always Include Request Parameter**
```php
// ❌ Wrong - No Request parameter
public function index()

// ✅ Correct - Include Request parameter
public function index(Request $request)
```

### **2. Use wantsJson() for Detection**
```php
if ($request->wantsJson()) {
    // Handle API request
} else {
    // Handle web request
}
```

### **3. Implement All Standard CRUD Methods**
- `index()` - List resources
- `create()` - Show create form/data
- `store()` - Create new resource
- `show()` - Show single resource
- `edit()` - Show edit form/data
- `update()` - Update resource
- `destroy()` - Delete resource

### **4. Proper HTTP Status Codes**
```php
// Success responses
200 - OK (general success)
201 - Created (resource created)
204 - No Content (deletion successful)

// Error responses
400 - Bad Request (validation failed)
422 - Unprocessable Entity (validation errors)
404 - Not Found (resource not found)
500 - Internal Server Error (unexpected errors)
```

### **5. Consistent API Response Structure**
```php
// Success response
return response()->json([
    'success' => true,
    'message' => 'Operation successful',
    'data' => $resourceData
], 201);

// Error response
return response()->json([
    'success' => false,
    'message' => 'Error description',
    'errors' => $validationErrors
], 422);
```

## 📚 **Best Practices Checklist**

### **Before Creating Controller:**
- [ ] Identify all relationships your model has
- [ ] List all fields that need validation
- [ ] Plan pagination for large datasets
- [ ] Consider what related data is needed for create/edit forms

### **When Writing Methods:**
- [ ] Add `Request $request` parameter to all methods
- [ ] Always implement both API and web response paths
- [ ] Include proper validation in store/update methods
- [ ] Add comprehensive error handling with try-catch
- [ ] Use appropriate HTTP status codes
- [ ] Load relationships when needed for API responses

### **For API Responses:**
- [ ] Use pagination for index methods
- [ ] Include related data with `with(['relations'])`
- [ ] Return consistent JSON structure
- [ ] Handle file uploads properly (multipart/form-data)

### **For Web Responses:**
- [ ] Return view with compact data
- [ ] Use route redirects for success messages
- [ ] Handle sessions for flash messages

## 🎯 **Common Pitfalls to Avoid**

### **❌ Don't Do This:**
```php
// Only handling one type of request
public function store(Request $request) {
    if ($request->has('api')) { // Don't do this!
        // API logic
    }
    // Web logic
}

// Missing error handling
public function update(Request $request, $id) {
    $resource = YourModel::findOrFail($id); // What if not found?
    $resource->update($request->all());
    return response()->json(['success' => true]);
}
```

### **✅ Do This Instead:**
```php
// Proper dual handling
public function store(Request $request) {
    try {
        $validated = $request->validate([...]);
        $resource = YourModel::create($validated);

        if ($request->wantsJson()) {
            return response()->json([...], 201);
        }
        
        return redirect()->route(...)->with('success', ...);
        
    } catch (ValidationException $e) {
        if ($request->wantsJson()) {
            return response()->json([...], 422);
        }
        throw $e;
    }
}
```

## 🚀 **Quick Setup Commands**

### **Generate Controller with Resource Methods:**
```bash
php artisan make:controller YourController --resource
```

### **Add API Routes (in routes/api.php):**
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('your-resources', YourController::class);
});
```

### **Add Web Routes (in routes/web.php):**
```php
Route::middleware('auth')->group(function () {
    Route::resource('your-resources', YourController::class);
});
```

## 💡 **Pro Tips**

1. **Use Resource Classes** - Create API Resources for consistent JSON formatting
2. **Implement Policies** - Add authorization for both web and API
3. **Add API Documentation** - Use tools like Laravel API Documentation Generator
4. **Test Both Paths** - Always test both web and API responses
5. **Handle File Uploads** - Ensure multipart/form-data works for APIs

## 🔧 **Tools & Helpers**

### **Create a Base Controller:**
```php
abstract class BaseController extends Controller
{
    protected function respond($data, $request)
    {
        if ($request->wantsJson()) {
            return response()->json($data);
        }
        return view($this->getView(), compact('data'));
    }
    
    protected function getView()
    {
        return 'dashboard.' . strtolower(class_basename($this));
    }
}
```

## 📝 **Example: Customer Controller**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $customers = Customer::paginate(15);
            return response()->json($customers);
        }

        $customers = Customer::all();
        return view('dashboard.customers.index', compact('customers'));
    }

    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Use POST /customers to create']);
        }

        return view('dashboard.customers.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'nullable|email|unique:customers,email',
                'address' => 'nullable|string',
            ]);

            $customer = Customer::create($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer created successfully',
                    'customer' => $customer
                ], 201);
            }

            return redirect()->route('dashboard.customers.index')
                           ->with('success', 'Customer created successfully');

        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
    }

    // Continue with show, update, destroy following the same pattern...
}
```

## 🧪 **Testing Your Controller**

### **Test Web Route:**
```bash
# Visit in browser
http://your-app.test/customers
```

### **Test API Route:**
```bash
# Using curl
curl -X GET http://your-app.test/api/customers \
     -H "Accept: application/json"

# Using Postman or similar tool
GET /api/customers
Accept: application/json
```

### **Test API Creation:**
```bash
curl -X POST http://your-app.test/api/customers \
     -H "Accept: application/json" \
     -H "Content-Type: application/json" \
     -d '{"name":"John Doe","phone":"1234567890","email":"john@example.com"}'
```

Following this guide will ensure every controller you create from now on will seamlessly handle both web and API requests! 🎉