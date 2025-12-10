<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Login Credentials</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .credentials { background: white; padding: 15px; border-left: 4px solid #4F46E5; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .warning { color: #DC2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Boutique Management System</h1>
        </div>

        <div class="content">
            <p>Dear {{ $staff->full_name }},</p>

            <p>Your account has been created in the Boutique Management System. Here are your login credentials:</p>

            <div class="credentials">
                <h3>Login Information:</h3>
                <p><strong>Email/Username:</strong> {{ $user->email }}</p>
                <p><strong>Temporary Password:</strong> {{ $tempPassword }}</p>
                <p><strong>Role:</strong> {{ $staff->role->role ?? 'Staff' }}</p>
                <p><strong>Staff ID:</strong> {{ $staff->staff_code }}</p>
            </div>

            <p class="warning">⚠️ Important: Please change your password after your first login for security purposes.</p>

            <p>You can access the system at: <a href="{{ url('/') }}">{{ url('/') }}</a></p>

            <p>If you have any questions, please contact your administrator.</p>

            <p>Best regards,<br>
            Boutique Management Team</p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>