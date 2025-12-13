# UserController Implementation Priority Guide

Based on the Flutter API Analysis Guide, you should implement **login functionality FIRST**, then the user account related work. Here's the implementation priority:

## 🔒 **Phase 1: Login Functionality (FIRST PRIORITY)**

### Why Login First?
1. **Authentication Dependency**: All UserController endpoints require valid Bearer tokens
2. **App Flow**: Mobile app must authenticate before accessing user data
3. **Foundation**: Token generation is needed for `/api/V1/account/me` and `/api/V1/user/updateStatus`

### Login Implementation Steps:

#### 1. **Check Current Login Route**
**File**: `routes/api.php` (Line 27)
```php
Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
```

#### 2. **Update AuthenticatedSessionController**
**Add this method:**
```php
// POST /api/V1/login (for mobile app)
public function apiLogin(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        'device_id' => 'required|string',
    ]);

    $credentials = $request->only('email', 'password');
    
    if (!Auth::attempt($credentials)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    $user = Auth::user();
    $token = $user->createToken('mobile-app')->plainTextToken;

    return response()->json([
        'success' => true,
        'data' => [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'email' => $user->email,
                'status' => $user->status ?? 'active',
                'employeeCode' => $user->employee_code ?? '',
                'designation' => $user->designation ?? '',
            ]
        ]
    ]);
}
```

#### 3. **Test Login Endpoint**
```bash
curl -X POST "http://your-domain.com/api/V1/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "staff@boutique.com",
    "password": "password123",
    "device_id": "android_device_123"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "token": "1|abcd1234...",
    "user": {
      "id": "1",
      "firstName": "John",
      "lastName": "Doe",
      "email": "john@boutique.com",
      "status": "active",
      "employeeCode": "EMP001",
      "designation": "Senior Tailor"
    }
  }
}
```

---

## 👤 **Phase 2: User Account Work (SECOND PRIORITY)**

### Why After Login?
- UserController endpoints require Bearer tokens
- You need a working authentication system first
- The mobile app will use the token from login to access user data

### After Login Works, Implement:

#### 1. **UserController `me()` Method**
```php
public function me(Request $request)
{
    $user = $request->user(); // Uses Bearer token from login
    
    return response()->json([
        'success' => true,
        'data' => [
            'id' => $user->id,
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'avatar' => $user->avatar_url ?? '',
            'gender' => $user->gender ?? '',
            'address' => $user->address ?? '',
            'phoneNumber' => $user->phone ?? '',
            'alternateNumber' => $user->alternate_phone ?? '',
            'status' => $user->status ?? 'active',
            'token' => $user->createToken('mobile-app')->plainTextToken,
            'employeeCode' => $user->employee_code ?? '',
            'email' => $user->email,
            'designation' => $user->designation ?? '',
            'isLocationActivityTrackingEnabled' => $user->location_tracking_enabled ?? false,
            'isApprover' => $user->is_approver ?? false,
            'isLeaveApprover' => $user->is_leave_approver ?? false,
            'isExpenseApprover' => $user->is_expense_approver ?? false,
        ]
    ]);
}
```

#### 2. **UserController `updateStatus()` Method**
```php
public function updateStatus(Request $request)
{
    $request->validate([
        'status' => 'required|string|in:online,offline,busy,away,on_call,do_not_disturb,on_leave,ON_meeting,unknown',
        'message' => 'nullable|string|max:255',
        'expires_at' => 'nullable|date|after:now',
    ]);

    $user = $request->user();
    
    $user->update([
        'status' => $request->status,
        'status_message' => $request->message,
        'status_expires_at' => $request->expires_at,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Status updated successfully'
    ]);
}
```

---

## 📋 **Implementation Priority Order**

### **Step 1: Login Implementation (This Week)**
1. ✅ **Update AuthenticatedSessionController** with `apiLogin()` method
2. ✅ **Test login endpoint** with Postman
3. ✅ **Verify token generation** works correctly
4. ✅ **Test authentication flow** end-to-end

### **Step 2: User Account Implementation (Next Week)**
1. ✅ **Implement UserController `me()` method**
2. ✅ **Implement UserController `updateStatus()` method** 
3. ✅ **Add database columns** for user fields
4. ✅ **Test user endpoints** with Bearer token

## 🧪 **Testing Strategy**

### **Phase 1 Tests:**
```bash
# 1. Test login (should return token)
POST /api/V1/login
# Expected: 200 OK with token

# 2. Test protected endpoint with token
GET /api/V1/account/me
Headers: Authorization: Bearer {token_from_step_1}
# Expected: 200 OK with user data
```

### **Phase 2 Tests:**
```bash
# 1. Update user status
POST /api/V1/user/updateStatus
Headers: Authorization: Bearer {token}
Body: {"status": "busy"}
# Expected: 200 OK with success message
```

---

## 💡 **Recommendation**

**Start with login functionality immediately** because:

1. **It's the foundation** - everything else depends on authentication
2. **It's simpler** - just one endpoint vs multiple user endpoints  
3. **Immediate feedback** - you can test if it's working right away
4. **Flutter app dependency** - the mobile app needs to login first before accessing any features

Once login is working and you can generate/validate tokens, then move to implementing the UserController methods. This logical flow will save you time and debugging effort.

---

## 📝 **Database Schema Requirements**

Your users table needs these columns:
```sql
ALTER TABLE users ADD COLUMN first_name VARCHAR(255);
ALTER TABLE users ADD COLUMN last_name VARCHAR(255);
ALTER TABLE users ADD COLUMN avatar_url VARCHAR(500);
ALTER TABLE users ADD COLUMN gender ENUM('male', 'female', 'other');
ALTER TABLE users ADD COLUMN address TEXT;
ALTER TABLE users ADD COLUMN phone VARCHAR(20);
ALTER TABLE users ADD COLUMN alternate_phone VARCHAR(20);
ALTER TABLE users ADD COLUMN status VARCHAR(20) DEFAULT 'active';
ALTER TABLE users ADD COLUMN employee_code VARCHAR(50);
ALTER TABLE users ADD COLUMN designation VARCHAR(255);
ALTER TABLE users ADD COLUMN location_tracking_enabled BOOLEAN DEFAULT false;
ALTER TABLE users ADD COLUMN is_approver BOOLEAN DEFAULT false;
ALTER TABLE users ADD COLUMN is_leave_approver BOOLEAN DEFAULT false;
ALTER TABLE users ADD COLUMN is_expense_approver BOOLEAN DEFAULT false;
ALTER TABLE users ADD COLUMN status_message VARCHAR(255);
ALTER TABLE users ADD COLUMN status_expires_at TIMESTAMP;
```

---

## 🔑 **Key Flutter Integration Points**

### UserModel Expected Fields:
```json
{
  "id": "1",
  "firstName": "John",
  "lastName": "Doe", 
  "avatar": "https://domain.com/storage/avatars/1.jpg",
  "gender": "male",
  "address": "123 Main St",
  "phoneNumber": "+91-9876543210",
  "alternateNumber": "+91-9876543211",
  "status": "active",
  "token": "1|abcd1234...",
  "employeeCode": "EMP001",
  "email": "john@boutique.com",
  "designation": "Senior Tailor",
  "isLocationActivityTrackingEnabled": true,
  "isApprover": false,
  "isLeaveApprover": false,
  "isExpenseApprover": false
}
```

### Status Update Request:
```json
{
  "status": "busy",
  "message": "In a meeting with client",
  "expires_at": "2025-12-12T18:00:00Z"
}
```

### Status Options:
- `online`
- `offline` 
- `busy`
- `away`
- `on_call`
- `do_not_disturb`
- `on_leave`
- `ON_meeting`
- `unknown`

---

## 🚀 **Next Steps**

1. **Start with login implementation** - it's the foundation
2. **Test thoroughly** with Postman before moving to next phase
3. **Implement UserController** only after login is working
4. **Follow the exact field names** from Flutter API Analysis Guide
5. **Use Bearer token authentication** for all protected endpoints

The Flutter API Analysis Guide shows you exactly what the mobile app expects, so follow the specifications closely for successful integration.