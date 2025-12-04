# Staff Mobile App Adaptation Guide

## Overview
This guide outlines the process of adapting the purchased HRMS employee mobile app (Flutter-based) for use in the Boutique Management System (BMS). The app will serve as the staff APK for attendance marking and task management.

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

## Required Backend Changes

### 1. Attendance APIs Implementation
The current BMS AttendanceController has placeholder implementations. Need to implement:

#### Core Attendance Endpoints
- `POST /api/V1/attendance/checkInOut` - Check in/out with location data
- `GET /api/V1/attendance/checkStatus` - Get current attendance status
- `GET /api/V1/attendance/getHistory` - Attendance history
- `POST /api/V1/attendance/statusUpdate` - Update attendance status
- `POST /api/V1/attendance/startStopBreak` - Break management
- `POST /api/V1/attendance/validateGeoLocation` - Geofence validation
- `POST /api/V1/attendance/setEarlyCheckoutReason` - Early checkout reasons

#### Database Requirements
Create `attendances` table with fields:
- id, staff_id, check_in_time, check_out_time, latitude, longitude
- battery_percentage, is_wifi_on, signal_strength, status
- break_start_time, break_end_time, late_reason, early_checkout_reason

### 2. Task Management APIs Implementation
Create new TaskController and Task model:

#### Task Endpoints
- `GET /api/V1/task/GetAll` - Get assigned tasks
- `POST /api/V1/task/startTask` - Start task with location
- `POST /api/V1/task/completeTask` - Complete task
- `POST /api/V1/task/holdTask` - Hold task
- `POST /api/V1/task/resumeTask` - Resume task
- `GET /api/V1/task/getTaskUpdates` - Task updates

#### Database Requirements
Create `tasks` table with fields:
- id, title, description, task_type, assigned_by_id, assigned_to_id
- client_id, latitude, longitude, is_geo_fence_enabled, max_radius
- start_date_time, end_date_time, status, for_date

### 3. Authentication & User Management
Implement missing auth endpoints:
- `POST /api/V1/login` - Staff login
- `GET /api/V1/account/me` - Get user profile
- `POST /api/V1/user/updateStatus` - Update user status

### 4. Additional Required APIs
- Notifications: `GET /api/V1/notification/getAll`
- Settings: `GET /api/V1/settings/getAppSettings`
- Dashboard: `GET /api/V1/getDashboardData`

## Implementation Steps

### Phase 1: Core Attendance System
1. Create attendance migration and model
2. Implement AttendanceController methods
3. Add authentication middleware for staff
4. Test check-in/check-out functionality

### Phase 2: Task Management System
1. Create task migration and model
2. Implement TaskController
3. Add task assignment functionality in web admin
4. Test task lifecycle (start → complete)

### Phase 3: Additional Features
1. Implement notification system
2. Add settings management
3. Configure Firebase for push notifications
4. Test offline sync capabilities

### Phase 4: App Customization
1. Update API base URL in `api_routes.dart`
2. Modify app branding (colors, logo)
3. Remove unnecessary features (leave, expenses, etc.)
4. Test end-to-end functionality

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

## Prerequisites in Current BMS Development

Before working on the staff mobile app, complete these BMS backend items first:

### 1. API Versioning Setup
- Add V1 API prefix to match HRMS app expectations (`/api/V1/` instead of `/api/`)
- Update routes/api.php to use `Route::prefix('V1')->group(function () { ... })`

### 2. Staff Authentication System
- Create separate staff login endpoint (current login is for admin users)
- Implement `/api/V1/login` for staff authentication
- Add staff authentication middleware
- Ensure Sanctum tokens work for staff sessions

### 3. Database Migrations
- Create `attendances` table migration
- Create `tasks` table migration
- Run migrations to set up database schema

### 4. Basic API Infrastructure
- Verify CORS configuration allows mobile app requests
- Test Sanctum authentication flow
- Ensure API responses match expected JSON format

### 5. Staff Management Completion
- Complete staff CRUD operations
- Ensure staff can be created with login credentials
- Test staff API endpoints

## Next Steps
1. Complete the 5 prerequisites above
2. Begin with Phase 1 (Attendance System) implementation
3. Set up test environment with mobile app
4. Configure app with new API endpoints
5. Conduct thorough testing
6. Deploy and monitor