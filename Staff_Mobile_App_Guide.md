# Staff Mobile App Development Guide

## Overview
Since you don't have Android development experience, here are the best approaches for creating a staff attendance and task management mobile app:

## 🎯 Recommended Approach: Flutter (Dart)

### Why Flutter?
- ✅ **Cross-platform**: One codebase for Android & iOS
- ✅ **Easy to learn**: Dart syntax similar to other languages
- ✅ **Fast development**: Hot reload, rich widgets
- ✅ **Laravel integration**: HTTP requests to your API
- ✅ **No Android/Java knowledge needed**

## 📱 App Features to Implement

### 1. **Authentication**
- Login with staff credentials
- JWT token storage
- Auto-login on app restart

### 2. **Attendance Marking**
- GPS location verification (optional)
- Check-in/Check-out with timestamps
- Daily attendance status
- Attendance history

### 3. **Task Management**
- View assigned tasks
- Update task status
- Submit work completion
- Task history

### 4. **Profile & Settings**
- View personal info
- Update profile picture
- Change password
- App settings

## 🚀 Getting Started with Flutter

### Prerequisites
```bash
# Install Flutter SDK
# https://flutter.dev/docs/get-started/install

# Install Android Studio (for Android emulator)
# https://developer.android.com/studio

# Verify installation
flutter doctor
```

### Create New Flutter Project
```bash
flutter create staff_app
cd staff_app
```

### Project Structure
```
staff_app/
├── lib/
│   ├── models/          # Data models (Staff, Task, Attendance)
│   ├── screens/         # UI screens (Login, Home, Tasks, etc.)
│   ├── services/        # API calls, local storage
│   ├── widgets/         # Reusable UI components
│   └── main.dart        # App entry point
├── android/             # Android-specific files
├── ios/                 # iOS-specific files
└── pubspec.yaml         # Dependencies
```

### Key Dependencies (pubspec.yaml)
```yaml
dependencies:
  flutter:
    sdk: flutter

  # HTTP requests
  http: ^1.1.0

  # State management
  provider: ^6.0.5

  # Local storage
  shared_preferences: ^2.2.0

  # Date/time handling
  intl: ^0.19.0

  # Image handling
  image_picker: ^1.0.4

  # Location services
  geolocator: ^10.1.0

  # Push notifications (optional)
  firebase_messaging: ^14.6.9
```

## 🔧 Core Implementation

### 1. API Service
```dart
class ApiService {
  final String baseUrl = 'http://your-laravel-app.com/api';

  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      body: {'email': email, 'password': password},
    );

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      throw Exception('Login failed');
    }
  }

  Future<Map<String, dynamic>> markAttendance(String token, String type) async {
    final response = await http.post(
      Uri.parse('$baseUrl/attendance'),
      headers: {'Authorization': 'Bearer $token'},
      body: {'type': type}, // 'check_in' or 'check_out'
    );

    return json.decode(response.body);
  }
}
```

### 2. Authentication Flow
```dart
class AuthProvider extends ChangeNotifier {
  String? _token;
  Staff? _currentStaff;

  Future<bool> login(String email, String password) async {
    try {
      final result = await ApiService().login(email, password);
      _token = result['token'];
      _currentStaff = Staff.fromJson(result['staff']);

      // Save to local storage
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('token', _token!);
      await prefs.setString('staff', json.encode(_currentStaff!.toJson()));

      notifyListeners();
      return true;
    } catch (e) {
      return false;
    }
  }
}
```

### 3. Attendance Screen
```dart
class AttendanceScreen extends StatefulWidget {
  @override
  _AttendanceScreenState createState() => _AttendanceScreenState();
}

class _AttendanceScreenState extends State<AttendanceScreen> {
  bool _isCheckedIn = false;
  DateTime? _checkInTime;
  DateTime? _checkOutTime;

  Future<void> _markAttendance(String type) async {
    final token = context.read<AuthProvider>().token;
    try {
      await ApiService().markAttendance(token!, type);
      setState(() {
        if (type == 'check_in') {
          _isCheckedIn = true;
          _checkInTime = DateTime.now();
        } else {
          _checkOutTime = DateTime.now();
        }
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Attendance marked successfully')),
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to mark attendance')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Attendance')),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(
              _isCheckedIn ? 'Checked In' : 'Not Checked In',
              style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
            ),
            SizedBox(height: 20),
            if (_checkInTime != null)
              Text('Check-in: ${_checkInTime!.toLocal()}'),
            if (_checkOutTime != null)
              Text('Check-out: ${_checkOutTime!.toLocal()}'),
            SizedBox(height: 40),
            ElevatedButton(
              onPressed: _isCheckedIn
                  ? () => _markAttendance('check_out')
                  : () => _markAttendance('check_in'),
              child: Text(_isCheckedIn ? 'Check Out' : 'Check In'),
              style: ElevatedButton.styleFrom(
                padding: EdgeInsets.symmetric(horizontal: 50, vertical: 15),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
```

## 📚 Learning Resources

### Flutter Documentation
- [Flutter Official Docs](https://flutter.dev/docs)
- [Dart Language Tour](https://dart.dev/guides/language/language-tour)
- [Flutter Codelabs](https://flutter.dev/docs/codelabs)

### Laravel API Integration
- [Laravel API Authentication](https://laravel.com/docs/api-authentication)
- [Flutter HTTP Package](https://pub.dev/packages/http)

### Recommended Learning Path
1. **Flutter Basics** (1-2 weeks)
   - Widgets, Layouts, Navigation
   - State Management

2. **API Integration** (1 week)
   - HTTP requests
   - JWT authentication
   - Error handling

3. **Attendance Feature** (1 week)
   - Location services
   - Time tracking
   - Offline support

4. **Task Management** (1-2 weeks)
   - CRUD operations
   - File uploads
   - Real-time updates

## 🔄 Alternative Approaches

### Option 2: React Native (if you know JavaScript)
```bash
npx react-native init StaffApp
```

### Option 3: Progressive Web App (PWA)
- Create responsive web interface
- Add service worker for offline support
- Installable on mobile devices

### Option 4: Hire Developer
- Use platforms like Upwork, Fiverr
- Provide API documentation
- Supervise development

## 💡 Tips for Success

1. **Start Small**: Begin with login and basic attendance
2. **Test Frequently**: Use real device/emulator testing
3. **Handle Offline**: Cache data for offline functionality
4. **Security**: Secure token storage, certificate pinning
5. **Updates**: Plan for app store deployments

## 🚀 Next Steps

1. Install Flutter and Android Studio
2. Complete Flutter's official tutorial
3. Create basic app structure
4. Implement authentication
5. Add attendance marking
6. Test with your Laravel API

Would you like me to help you set up the Flutter project structure or implement any specific feature?