# Flutter App API Analysis Guide - Understanding Mobile App Requirements

## Overview
This guide shows you how to analyze the Flutter app code to understand exactly what REST API endpoints, request formats, and response structures the mobile app expects. This is crucial for implementing the correct backend API.

## 🎯 UserController Specific Analysis

### Required Endpoints for UserController

Based on analysis of the Flutter code, your UserController needs to implement these two endpoints:

1. **GET /api/V1/account/me** - Get current user profile
2. **POST /api/V1/user/updateStatus** - Update user status

### Step 1: Analyze UserModel (What the app expects)

**File**: `staff_app/lib/models/user_model.dart`

```dart
class UserModel {
  // All these fields MUST be present in your API response
  String? id,
      firstName,
      lastName,
      avatar,
      gender,
      address,
      phoneNumber,
      alternateNumber,
      status,
      token,
      email,
      designation,
      employeeCode;

  bool? locationActivityTrackingEnabled,
      isApprover,
      isLeaveApprover,
      isExpenseApprover;

  // This is how Flutter parses the JSON response
  factory UserModel.fromJSON(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'].toString(),
      firstName: json['firstName'].toString(),
      lastName: json['lastName'].toString(),
      avatar: json['avatar'].toString(),
      gender: json['gender'].toString(),
      address: json['address'].toString(),
      phoneNumber: json['phoneNumber'].toString(),
      alternateNumber: json['alternateNumber'].toString(),
      status: json['status'].toString(),
      token: json['token'].toString(),
      employeeCode: json['employeeCode'].toString(),
      email: json['email'].toString(),
      designation: json['designation'].toString(),
      locationActivityTrackingEnabled:
          json['isLocationActivityTrackingEnabled'] ?? false,
      isApprover: json['isApprover'] ?? false,
      isLeaveApprover: json['isLeaveApprover'] ?? false,
      isExpenseApprover: json['isExpenseApprover'] ?? false,
    );
  }
}
```

**Critical Analysis**: The app expects these exact field names in your JSON response:
- `id`, `firstName`, `lastName`, `avatar`, `gender`, `address`
- `phoneNumber`, `alternateNumber`, `status`, `token`, `email`
- `designation`, `employeeCode`
- `isLocationActivityTrackingEnabled`, `isApprover`, `isLeaveApprover`, `isExpenseApprover`

### Step 2: Check API Service Calls

**File**: `staff_app/lib/api/api_service.dart`

#### User Profile Endpoint Call
```dart
// Line 1590-1596: This is how app calls the user profile endpoint
Future<UserModel?> me() async {
  var response = await handleResponse(await getRequest(APIRoutes.meURL));
  if (!checkSuccessCase(response)) {
    return null;
  }
  return UserModel.fromJSON(response?.data);
}
```

**What this tells you:**
- Endpoint: `APIRoutes.meURL` which is `'account/me'`
- Method: GET request
- Response: JSON object with user data
- Authentication: Uses Bearer token

#### User Status Update Endpoint Call
```dart
// Line 305-320: This is how app updates user status
Future<bool> updateUserStatus(
  String status, {
  String? message,
  DateTime? expiresAt,
}) async {
  Map<String, dynamic> payload = {
    'status': status,
    'message': message ?? '',
    'expires_at': expiresAt?.toIso8601String(),
  };

  var response = await handleResponse(
    await postRequest(APIRoutes.updateUserStatus, payload),
  );
  return checkSuccessCase(response, showError: true);
}
```

**What this tells you:**
- Endpoint: `APIRoutes.updateUserStatus` which is `'user/updateStatus'`
- Method: POST request
- Request body: `{status, message?, expires_at?}`
- Expected response: Success boolean

### Step 3: Check Login Flow (How user data is used)

**File**: `staff_app/lib/screens/Login/LoginStore.dart`

```dart
// Line 118-182: Login method showing how user data is processed
Future<String> login() async {
  isLoading = true;
  Map payload = {
    'employeeId': employeeId!.trim(),
    'password': password!.trim()
  };

  // Make login request
  var user = UserModel.fromJSON(apiResponse.data);

  // App saves ALL these fields to local storage
  await setValue(userIdPref, user.id);
  await setValue(firstNamePref, user.firstName);
  await setValue(lastNamePref, user.lastName);
  await setValue(genderPref, user.gender);
  if (!user.avatar.isEmptyOrNull) {
    await setValue(avatarPref, user.avatar ?? '');
  }

  await setValue(locationActivityTrackingEnabledPref,
      user.locationActivityTrackingEnabled);

  await setValue(employeeCodePref, user.employeeCode);

  await setValue(addressPref, user.address);
  await setValue(phoneNumberPref, user.phoneNumber);
  await setValue(alternateNumberPref, user.alternateNumber);
  await setValue(statusPref, user.status);
  await setValue(tokenPref, user.token);
  await setValue(emailPref, user.email);
  await setValue(designationPref, user.designation);
  await setValue(approverPref, user.isApprover);
}
```

### Step 4: Check App Store Usage

**File**: `staff_app/lib/store/AppStore.dart`

```dart
// Line 234-255: How app fetches and updates user status
Future<void> fetchUserStatus(int? userId) async {
  var result = await apiService.getUserStatus(userId);
  if (result != null) {
    userStatusModel = result;
    currentUserStatus = userStatusModel!.status;
    statusMessage = userStatusModel!.message;
  } else {
    currentUserStatus = 'unknown';
  }
}

Future<void> updateUserStatus(String status, {String? message}) async {
  var success = await apiService.updateUserStatus(status, message: message);
  if (success) {
    currentUserStatus = status;
    statusMessage = message;
    toast('Status updated to $status');
  } else {
    toast('Failed to update status');
  }
}
```

## Complete Implementation Guide

### Backend Implementation Required

#### 1. GET /api/V1/account/me Implementation

**Laravel Controller Method:**
```php
// app/Http/Controllers/UserController.php
public function me(Request $request)
{
    $user = $request->user(); // Sanctum authenticated user

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }

    // Return user data with EXACT field names expected by Flutter
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

#### 2. POST /api/V1/user/updateStatus Implementation

**Laravel Controller Method:**
```php
public function updateStatus(Request $request)
{
    $request->validate([
        'status' => 'required|string|in:online,offline,busy,away,on_call,do_not_disturb,on_leave,ON_meeting,unknown',
        'message' => 'nullable|string|max:255',
        'expires_at' => 'nullable|date|after:now',
    ]);

    $user = $request->user();
    
    // Update user status in database
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

### Database Schema Requirements

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

### Testing Your Implementation

#### Test 1: User Profile Endpoint
```bash
curl -X GET "http://your-domain.com/api/V1/account/me" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Accept: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "id": "1",
    "firstName": "John",
    "lastName": "Doe",
    "avatar": "https://domain.com/storage/avatars/1.jpg",
    "gender": "male",
    "address": "123 Main St, City",
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
}
```

#### Test 2: Update User Status
```bash
curl -X POST "http://your-domain.com/api/V1/user/updateStatus" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "busy",
    "message": "In a meeting with client"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Status updated successfully"
}
```

### Flutter App Integration Points

#### Where User Data is Used in App:
1. **Login Screen**: Parses user data and saves to local storage
2. **Dashboard**: Shows user name, avatar, designation
3. **Profile Screen**: Displays all user information
4. **Status Management**: Updates and displays current status
5. **Navigation**: Uses user role and permissions

#### Key SharedPreferences Keys Used:
- `userIdPref` - User ID
- `firstNamePref` - First name
- `lastNamePref` - Last name  
- `avatarPref` - Profile picture URL
- `emailPref` - Email address
- `designationPref` - Job designation
- `employeeCodePref` - Employee code
- `statusPref` - Current status
- `approverPref` - Is approver boolean

## Method 1: Analyze API Routes Configuration

### 1. Check API Endpoints List
**File**: `staff_app/lib/api/api_routes.dart`

This file contains all the API endpoints the app expects. Let's analyze the key ones:

```dart
class APIRoutes {
  static const baseURL = "http://10.0.2.2:8000/api/V1/";
  
  // Authentication
  static const loginURL = 'login';
  static const meURL = 'account/me';
  static const updateUserStatus = 'user/updateStatus';
  
  // Attendance
  static const checkInOut = 'attendance/checkInOut';
  static const checkAttendanceStatus = 'attendance/checkStatus';
  static const getAttendanceHistory = 'attendance/getHistory';
  static const startStopBreak = 'attendance/startStopBreak';
  static const validateGeoLocation = 'attendance/validateGeoLocation';
  
  // Tasks
  static const getTasks = 'task/GetAll';
  static const startTask = 'task/startTask';
  static const completeTask = 'task/completeTask';
  static const holdTask = 'task/holdTask';
  static const resumeTask = 'task/resumeTask';
  
  // Notifications
  static const getNotifications = 'notification/getAll';
  
  // Settings
  static const getAppSettings = 'settings/getAppSettings';
  static const getDashboardData = 'getDashboardData';
}
```

**What this tells you:**
- Base URL structure: `http://domain/api/V1/`
- Authentication uses `/login` and `/account/me`
- All endpoints are under `/api/V1/` prefix
- Attendance has 6 different endpoints
- Task management has 5 endpoints

## Method 2: Analyze API Service Implementation

### 2. Check API Service Calls
**File**: `staff_app/lib/api/api_service.dart`

Look for how the app makes HTTP requests:

```dart
// Example from the app
class ApiService {
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  
  Future<Map<String, dynamic>> postRequest(String url, Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse(APIRoutes.baseURL + url),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${await getToken()}',
        },
        body: json.encode(data),
      );
      
      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('API Error: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Network Error: $e');
    }
  }
}
```

**Key insights:**
- Uses `application/json` content type
- Requires Bearer token authentication
- Expects JSON responses
- Throws exceptions for non-200 status codes

## Method 3: Analyze Request Models

### 3. Check How Data is Sent to API
Look for request models in the app:

**Example from Attendance Store**:
```dart
// staff_app/lib/screens/Attendance/AttendanceStore.dart
class AttendanceStore = _AttendanceStore with _$AttendanceStore;

abstract class _AttendanceStore with Store {
  
  @action
  Future<void> checkInOut(String action) async {
    try {
      // Prepare request data
      final locationData = await LocationService.getCurrentLocation();
      final deviceInfo = await DeviceService.getDeviceInfo();
      
      final requestData = {
        'action': action, // 'check_in' or 'check_out'
        'latitude': locationData.latitude,
        'longitude': locationData.longitude,
        'address': locationData.address,
        'battery_level': deviceInfo.batteryLevel,
        'device_id': deviceInfo.deviceId,
        'app_version': deviceInfo.appVersion,
        'timestamp': DateTime.now().toIso8601String(),
      };
      
      // Make API call
      final response = await ApiService.postRequest(
        APIRoutes.checkInOut, 
        requestData
      );
      
      if (response['success']) {
        // Update local state
        setCheckInStatus(response['data']);
      }
    } catch (e) {
      setError('Check-in failed: $e');
    }
  }
}
```

**Request structure for checkInOut:**
```json
{
  "action": "check_in", // or "check_out"
  "latitude": 12.9716,
  "longitude": 77.5946,
  "address": "Bangalore, KA",
  "battery_level": 85,
  "device_id": "device_unique_id",
  "app_version": "1.0.0",
  "timestamp": "2025-12-12T09:00:00Z"
}
```

### 4. Task Management Request Example
```dart
// staff_app/lib/screens/Task/task_store.dart
@action
Future<void> startTask(int taskId) async {
  try {
    final locationData = await LocationService.getCurrentLocation();
    
    final requestData = {
      'task_id': taskId,
      'latitude': locationData.latitude,
      'longitude': locationData.longitude,
      'address': locationData.address,
      'started_at': DateTime.now().toIso8601String(),
      'notes': 'Task started',
    };
    
    final response = await ApiService.postRequest(
      APIRoutes.startTask,
      requestData
    );
    
    if (response['success']) {
      // Update task status in local state
      updateTaskStatus(taskId, 'in_progress');
    }
  } catch (e) {
    setError('Failed to start task: $e');
  }
}
```

**Request structure for startTask:**
```json
{
  "task_id": 123,
  "latitude": 12.9716,
  "longitude": 77.5946,
  "address": "MG Road, Bangalore",
  "started_at": "2025-12-12T09:00:00Z",
  "notes": "Task started"
}
```

## Method 4: Analyze Response Models

### 5. Check Expected Response Structures
**File**: `staff_app/lib/models/status/status_response.dart`

```dart
class StatusResponse {
  String? status = 'new';
  String? checkInAt;
  String? checkOutAt;
  String? userStatus;
  String? shiftStartAt;
  String? shiftEndAt;
  bool? isLate;
  bool? isOnLeave;
  bool? isOnBreak;
  String? breakStartedAt;
  num? travelledDistance;
  String? attendanceType;
  num? trackedHours;
  bool? isSiteEmployee;
  String? siteName;
  String? siteAttendanceType;
  String? deviceStatus;
  
  StatusResponse.fromJson(Map<String, dynamic> json) {
    status = json['status'];
    checkInAt = json['checkInAt'];
    checkOutAt = json['checkOutAt'];
    userStatus = json['userStatus'];
    shiftStartAt = json['shiftStartTime']; // Note: different key name!
    shiftEndAt = json['shiftEndTime']; // Note: different key name!
    // ... more fields
  }
}
```

**Expected response for attendance status:**
```json
{
  "success": true,
  "data": {
    "status": "checked_in",
    "checkInAt": "2025-12-12T09:00:00Z",
    "checkOutAt": null,
    "userStatus": "active",
    "shiftStartTime": "09:00", // Note: not "shiftStartAt"!
    "shiftEndTime": "18:00", // Note: not "shiftEndAt"!
    "isLate": false,
    "isOnLeave": false,
    "isOnBreak": false,
    "breakStartedAt": null,
    "travelledDistance": 0,
    "trackedHours": 6.5,
    "attendanceType": "gps",
    "isSiteEmployee": false,
    "siteName": null,
    "siteAttendanceType": null,
    "deviceStatus": "verified"
  }
}
```

### 6. Task Model Analysis
**File**: `staff_app/lib/models/Task/task_model.dart`

```dart
class TaskModel {
  int? id;
  String? title;
  String? description;
  String? taskType; // Important: This is the task type field
  int? assignedById;
  int? clientId;
  ClientModel? client;
  double? latitude;
  double? longitude;
  bool? isGeoFenceEnabled;
  int? maxRadius;
  String? startDateTime;
  String? endDateTime;
  String? status; // Task status: new, in_progress, hold, completed
  String? forDate;
}
```

**Expected response for tasks list:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Take measurements for Wedding Dress",
      "description": "Visit Mrs. Sharma for final measurements",
      "taskType": "measurement", // Expected values: measurement, delivery, etc.
      "assignedById": 5,
      "clientId": 15,
      "client": {
        "id": 15,
        "name": "Mrs. Sharma",
        "phone": "+91-9876543210",
        "address": "MG Road, Bangalore"
      },
      "latitude": 12.9716,
      "longitude": 77.5946,
      "isGeoFenceEnabled": true,
      "maxRadius": 100,
      "startDateTime": "2025-12-12T10:00:00Z",
      "endDateTime": "2025-12-12T11:00:00Z",
      "status": "new", // Expected: new, in_progress, hold, completed
      "forDate": "2025-12-12"
    }
  ]
}
```

## Method 5: Analyze Authentication Flow

### 7. Check Login Implementation
**File**: `staff_app/lib/screens/Login/LoginStore.dart`

```dart
@action
Future<String> login(String email, String password) async {
  try {
    setLoading(true);
    
    final requestData = {
      'email': email,
      'password': password,
      'device_id': await DeviceService.getDeviceId(),
      'device_type': Platform.operatingSystem,
      'app_version': await AppInfo.getVersion(),
    };
    
    final response = await ApiService.postRequest(
      APIRoutes.loginURL,
      requestData
    );
    
    if (response['success']) {
      // Save token
      await SharedPreferences.getInstance().setString('token', response['data']['token']);
      
      // Save user data
      final userData = response['data']['user'];
      await UserStore.saveUserData(userData);
      
      setUser(UserModel.fromJson(userData));
    }
  } catch (e) {
    setError('Login failed: $e');
  }
}
```

**Expected login response:**
```json
{
  "success": true,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@boutique.com",
      "role": "staff",
      "permissions": ["attendance", "tasks", "customers"],
      "profile_image": "https://domain.com/storage/profile/1.jpg",
      "phone": "+91-9876543210",
      "department": "Tailoring",
      "designation": "Senior Tailor"
    }
  }
}
```

## Method 6: Analyze Error Handling

### 8. Check How App Handles API Errors
```dart
// Common error response structure expected by the app
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid.",
    "details": {
      "email": ["The email field is required."],
      "password": ["The password field is required."]
    }
  }
}
```

## Method 7: Analyze Notification Structure

### 9. Check Notifications Response
**File**: `staff_app/lib/models/notification_model.dart`

```dart
class NotificationModel {
  int? id;
  String? title;
  String? message;
  String? type;
  String? createdAt;
  bool? isRead;
  Map<String, dynamic>? data; // Additional data payload
  
  NotificationModel.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    title = json['title'];
    message = json['message'];
    type = json['type'];
    createdAt = json['created_at'];
    isRead = json['is_read'];
    data = json['data'];
  }
}
```

**Expected notifications response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "New Task Assigned",
      "message": "You have been assigned a new measurement task",
      "type": "task_assigned",
      "created_at": "2025-12-12T08:30:00Z",
      "is_read": false,
      "data": {
        "task_id": 123,
        "priority": "high",
        "customer_name": "Mrs. Sharma"
      }
    },
    {
      "id": 2,
      "title": "Order Ready for Delivery",
      "message": "Order #ORD-001 is ready for delivery",
      "type": "order_ready",
      "created_at": "2025-12-12T07:15:00Z",
      "is_read": true,
      "data": {
        "order_id": "ORD-001",
        "customer_address": "MG Road, Bangalore"
      }
    }
  ]
}
```

## Quick Reference: API Endpoint Analysis

### UserController Endpoints (From Analysis)
| Endpoint | Method | Request | Response |
|----------|--------|---------|----------|
| `/api/V1/account/me` | GET | Bearer token | User profile data |
| `/api/V1/user/updateStatus` | POST | status, message?, expires_at? | Success response |

### Authentication Endpoints
| Endpoint | Method | Request | Response |
|----------|--------|---------|----------|
| `/api/V1/login` | POST | email, password, device_id | token, user data |
| `/api/V1/account/me` | GET | Bearer token | user profile |

### Attendance Endpoints
| Endpoint | Method | Request | Response |
|----------|--------|---------|----------|
| `/api/V1/attendance/checkInOut` | POST | action, latitude, longitude, address | success, status |
| `/api/V1/attendance/checkStatus` | GET | Bearer token | attendance status |
| `/api/V1/attendance/getHistory` | GET | page, limit | paginated history |
| `/api/V1/attendance/startStopBreak` | POST | action, timestamp | break status |
| `/api/V1/attendance/validateGeoLocation` | POST | latitude, longitude | validation result |
| `/api/V1/attendance/setEarlyCheckoutReason` | POST | reason, timestamp | success |

### Task Endpoints
| Endpoint | Method | Request | Response |
|----------|--------|---------|----------|
| `/api/V1/task/GetAll` | GET | Bearer token | task list |
| `/api/V1/task/startTask` | POST | task_id, location, notes | success, updated task |
| `/api/V1/task/completeTask` | POST | task_id, notes, location | success, completed task |
| `/api/V1/task/holdTask` | POST | task_id, reason | success, updated task |
| `/api/V1/task/resumeTask` | POST | task_id | success, updated task |
| `/api/V1/task/getTaskUpdates` | GET | task_id | task history |

### Other Endpoints
| Endpoint | Method | Request | Response |
|----------|--------|---------|----------|
| `/api/V1/notification/getAll` | GET | Bearer token | notification list |
| `/api/V1/settings/getAppSettings` | GET | Bearer token | app configuration |
| `/api/V1/getDashboardData` | GET | Bearer token | dashboard metrics |
| `/api/V1/user/updateStatus` | POST | status | success, updated status |

## How to Implement Backend Based on This Analysis

### 1. Create Controllers with Correct Method Signatures
```php
// app/Http/Controllers/UserController.php
class UserController extends Controller
{
    // GET /api/V1/account/me
    public function me(Request $request)
    {
        $user = $request->user(); // Sanctum authenticated user
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'avatar' => $user->avatar_url ?? '',
                // ... all fields expected by the app
            ]
        ]);
    }
    
    // POST /api/V1/user/updateStatus
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:online,offline,busy,away,on_call,do_not_disturb,on_leave,ON_meeting,unknown',
            'message' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);
        
        // Implementation here
        
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
}
```

### 2. Return Correct Response Structure
```php
// Always return in this format
return response()->json([
    'success' => true, // or false for errors
    'data' => [...], // actual data
    'message' => 'Operation successful' // optional
]);
```

### 3. Handle Authentication Correctly
```php
// In your controllers
public function me(Request $request)
{
    $user = $request->user(); // Sanctum authenticated user
    
    return response()->json([
        'success' => true,
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            // ... all fields expected by the app
        ]
    ]);
}
```

## Testing Your Implementation

### Use Postman with These Headers
```
Content-Type: application/json
Authorization: Bearer your_jwt_token_here
Accept: application/json
```

### Sample Test Requests

**1. Login Test**
```json
POST /api/V1/login
{
  "email": "staff@boutique.com",
  "password": "password123",
  "device_id": "android_device_123"
}
```

**2. Check User Profile**
```
GET /api/V1/account/me
Authorization: Bearer token_here
```

**3. Update User Status**
```json
POST /api/V1/user/updateStatus
{
  "status": "busy",
  "message": "In a meeting with client"
}
```

**4. Check Attendance Status**
```
GET /api/V1/attendance/checkStatus
Authorization: Bearer token_here
```

**5. Check-in**
```json
POST /api/V1/attendance/checkInOut
{
  "action": "check_in",
  "latitude": 12.9716,
  "longitude": 77.5946,
  "address": "MG Road, Bangalore",
  "battery_level": 85,
  "device_id": "android_device_123",
  "timestamp": "2025-12-12T09:00:00Z"
}
```

This analysis shows you exactly what the Flutter app expects from your REST API. Use this guide to implement your backend controllers with the correct request/response formats.