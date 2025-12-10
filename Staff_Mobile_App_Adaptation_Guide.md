# Staff Mobile App Adaptation Guide - UPDATED

## Overview
This guide outlines the process of adapting the purchased HRMS employee mobile app (Flutter-based) for use in the Boutique Management System (BMS). The app will serve as the staff APK for attendance marking and task management.

## Current BMS Status Analysis

### ✅ Already Implemented (Good Progress!)
- **Database Models**: `Attendance` and `Task` models with proper relationships and features
- **API Routes**: V1 structure already set up in `routes/api.php` with attendance/task endpoints
- **Database Migrations**: `attendances` and `tasks` tables created
- **API Versioning**: V1 prefix structure is in place

### ❌ Still Missing Implementation (Need to Complete)

#### Backend API Endpoints (Critical)

**1. AttendanceController API Methods** - Currently have placeholder implementations:
```php
// Need to implement these methods in AttendanceController:
- apiCheckInOut() - Handle check-in/out with location data
- apiCheckStatus() - Return current attendance status
- apiGetHistory() - Attendance history with pagination
- apiStatusUpdate() - Update attendance status
- apiStartStopBreak() - Break management
- apiValidateGeoLocation() - Geofence validation
- apiSetEarlyCheckoutReason() - Early checkout reasons
```

**2. TaskController API Methods** - Currently empty:
```php
// Need to implement these methods in TaskController:
- apiGetAllTasks() - Get assigned tasks for staff
- apiStartTask() - Start task with location tracking
- apiCompleteTask() - Complete task with notes
- apiHoldTask() - Hold task with reason
- apiResumeTask() - Resume held task
- apiGetTaskUpdates() - Task update history
```

**3. UserController Implementation** - Currently empty:
```php
// Need to implement these methods in UserController:
- me() - Get current staff profile
- updateStatus() - Update staff status
```

**4. Authentication System** - Need staff-specific login:
- Implement staff login in `AuthenticatedSessionController::apiLogin()`
- Handle authentication for staff vs admin users
- Return proper JWT tokens for mobile app

#### Flutter App Changes (Easy - Just Configuration)

**1. API Base URL Configuration**
```dart
// In lib/api/api_routes.dart, change line 5:
static const baseURL = "http://your-bms-domain/api/V1/";
```

**2. Response Model Implementation**
The app expects these response models that need backend compatibility:
- `StatusResponse` for attendance status
- `UserModel` for user profiles  
- `DashboardModel` for dashboard data
- `NotificationResponse` for notifications

#### Additional Backend Controllers

**5. NotificationController** - Need to implement:
```php
- getAll() - Get staff notifications
```

**6. SettingsController** - Need to implement:
```php
- getAppSettings() - Get app configuration
```

## HRMS App Analysis

### Current Features Available
The HRMS app includes comprehensive features that can be leveraged for boutique staff:

#### Attendance Management
- **Check-in/Check-out**: Location-based attendance with GPS coordinates, battery level, connectivity status
- **Attendance Types**: Supports geofence, IP address, QR code, dynamic QR, and face recognition
- **Break Management**: Start/stop break functionality
- **Attendance History**: View past attendance records
- **Real-time Tracking**: Background location tracking with activity recognition
- **Late Check-in Reasons**: Ability to provide reasons for late arrivals
- **Early Checkout Reasons**: Reason submission for early departures

#### Task Management
- **Task Assignment**: Tasks with title, description, type, assigned by, client association
- **Task Status Tracking**: New → In Progress → Hold → Completed workflow
- **Location-based Tasks**: GPS tracking for task start/completion
- **Geofencing**: Optional geofence restrictions for tasks
- **Task History**: Complete task history with timestamps

#### Additional Features
- User authentication and profile management
- Push notifications via Firebase
- Offline mode with sync capabilities
- Chat/messaging system
- Document management
- Expense tracking
- Leave management
- Device verification and security

### App Architecture
- **Framework**: Flutter (Dart)
- **State Management**: MobX
- **Backend Communication**: REST API
- **Database**: Local storage with Hive
- **Background Services**: Location tracking, activity recognition
- **Authentication**: JWT tokens
- **Maps Integration**: Google Maps

## Implementation Priority (Critical → Nice-to-have)

### Phase 1: Core Authentication (Critical)
1. Complete `UserController::me()` method
2. Implement staff-specific login in `AuthenticatedSessionController`
3. Test staff authentication flow

### Phase 2: Attendance System (Critical)
1. Implement `AttendanceController::apiCheckInOut()`
2. Implement `AttendanceController::apiCheckStatus()`
3. Test attendance check-in/check-out functionality

### Phase 3: Task Management (Important)
1. Implement `TaskController::apiGetAllTasks()`
2. Implement task lifecycle methods (start, complete, hold, resume)
3. Test task assignment and tracking

### Phase 4: Supporting Features (Important)
1. Implement notification system
2. Add dashboard data endpoint
3. Add app settings endpoint

### Phase 5: Flutter App Customization (Easy)
1. Update API base URL
2. Modify branding (colors, logos)
3. Remove unnecessary HRMS features
4. Test end-to-end functionality

## Immediate Next Steps

**Focus on Phase 1 & 2 first** - these are blocking the mobile app from working:

1. **Complete UserController methods** for staff authentication
2. **Implement core attendance API methods** for check-in/check-out
3. **Test authentication flow** with mobile app
4. **Update Flutter app API URL** to point to your BMS domain

Once these are working, staff can at least log in and mark attendance. Task management can be added in the next phase.

## Major Considerations

### Security
- Implement proper JWT authentication
- Add role-based access control
- Secure API endpoints with middleware

### Performance
- Optimize location tracking frequency
- Implement efficient data sync
- Add proper error handling

### User Experience
- Simplify complex HRMS features for boutique use
- Ensure intuitive attendance marking
- Clear task assignment and tracking

### Maintenance
- Keep app and backend versions in sync
- Regular security updates
- Monitor API performance

## Testing Checklist
- [ ] Staff login functionality
- [ ] Attendance check-in/check-out
- [ ] Task assignment and status updates
- [ ] Location tracking accuracy
- [ ] Offline mode functionality
- [ ] Push notifications
- [ ] Data synchronization

## App Modifications Needed

### API Configuration
Change in `lib/api/api_routes.dart`:
```dart
static const baseURL = "http://your-bms-domain/api/V1/";
```

### Branding Changes
- Update app name and package name
- Replace logos in `images/` directory
- Modify color scheme in `lib/utils/app_colors.dart`
- Update splash screen

### Feature Removal/Customization
- Remove HRMS-specific features not needed for boutique
- Customize task types for boutique operations
- Modify UI text for boutique context