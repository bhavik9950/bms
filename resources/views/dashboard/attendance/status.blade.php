@extends('layouts.app')
@section('title', 'Attendance Status')
@section('content')

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">Attendance Status</h1>
            <h5 class="text-xs text-gray-500">View your current attendance status</h5>
        </div>

        <a href="{{ route('dashboard.attendance.index') }}"
            class="bg-red-400 hover:bg-red-600 text-white px-4 py-2 rounded-md">
            <i class="ti ti-arrow-left mr-2"></i>
            Back to Attendance
        </a>
    </div>

    {{-- Current Status Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Today's Status --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Today's Status</h3>
                    <p class="text-sm text-gray-600 mt-1" id="current-date">{{ now()->format('l, F j, Y') }}</p>
                </div>
                <div id="today-status-badge">
                    <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-600">Not Checked In</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-sm mb-1">
                    <span>Check-in Time:</span>
                    <span id="check-in-time">-</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span>Check-out Time:</span>
                    <span id="check-out-time">-</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Total Hours:</span>
                    <span id="total-hours">-</span>
                </div>
            </div>
        </div>

        {{-- Weekly Summary --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">This Week</h3>
                    <p class="text-sm text-gray-600 mt-1">Current week summary</p>
                </div>
                <i class="ti ti-calendar-week text-3xl text-blue-500"></i>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span>Present Days:</span>
                    <span id="week-present">0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Absent Days:</span>
                    <span id="week-absent">0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Total Hours:</span>
                    <span id="week-hours">0.0</span>
                </div>
            </div>
        </div>

        {{-- Monthly Summary --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">This Month</h3>
                    <p class="text-sm text-gray-600 mt-1">Current month summary</p>
                </div>
                <i class="ti ti-calendar-month text-3xl text-green-500"></i>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span>Present Days:</span>
                    <span id="month-present">0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Absent Days:</span>
                    <span id="month-absent">0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Total Hours:</span>
                    <span id="month-hours">0.0</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('dashboard.attendance.checkInOut') }}"
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="ti ti-login text-2xl text-green-600 mr-3"></i>
                <div>
                    <p class="font-medium">Check In/Out</p>
                    <p class="text-sm text-gray-600">Mark attendance</p>
                </div>
            </a>

            <a href="{{ route('dashboard.attendance.break') }}"
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="ti ti-coffee text-2xl text-orange-600 mr-3"></i>
                <div>
                    <p class="font-medium">Break Management</p>
                    <p class="text-sm text-gray-600">Start/stop breaks</p>
                </div>
            </a>

            <a href="{{ route('dashboard.attendance.history') }}"
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="ti ti-history text-2xl text-blue-600 mr-3"></i>
                <div>
                    <p class="font-medium">View History</p>
                    <p class="text-sm text-gray-600">Past attendance</p>
                </div>
            </a>

            <a href="{{ route('dashboard.attendance.statusUpdate') }}"
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="ti ti-edit text-2xl text-purple-600 mr-3"></i>
                <div>
                    <p class="font-medium">Update Status</p>
                    <p class="text-sm text-gray-600">Modify attendance</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
        <div id="recent-activity" class="space-y-3">
            <div class="text-center text-gray-500 py-8">
                <i class="ti ti-clock text-3xl mb-2"></i>
                <p>Loading recent activity...</p>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for status updates --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const todayStatusBadge = document.getElementById('today-status-badge');
    const checkInTime = document.getElementById('check-in-time');
    const checkOutTime = document.getElementById('check-out-time');
    const totalHours = document.getElementById('total-hours');
    const recentActivity = document.getElementById('recent-activity');

    // Mock data - replace with actual API calls
    const mockData = {
        today: {
            status: 'checked_in',
            checkIn: '09:15 AM',
            checkOut: null,
            totalHours: null
        },
        week: {
            present: 4,
            absent: 1,
            totalHours: 32.5
        },
        month: {
            present: 18,
            absent: 3,
            totalHours: 144.0
        },
        recentActivity: [
            {
                type: 'check_in',
                time: '2025-12-04 09:15:00',
                location: 'Main Office'
            },
            {
                type: 'break_start',
                time: '2025-12-04 12:30:00',
                location: 'Main Office'
            },
            {
                type: 'break_end',
                time: '2025-12-04 13:00:00',
                location: 'Main Office'
            }
        ]
    };

    // Update today's status
    function updateTodayStatus() {
        const status = mockData.today.status;
        let badgeClass = 'bg-gray-100 text-gray-600';
        let badgeText = 'Not Checked In';

        if (status === 'checked_in') {
            badgeClass = 'bg-green-100 text-green-600';
            badgeText = 'Checked In';
        } else if (status === 'checked_out') {
            badgeClass = 'bg-blue-100 text-blue-600';
            badgeText = 'Checked Out';
        }

        todayStatusBadge.innerHTML = `<span class="px-3 py-1 text-sm rounded-full ${badgeClass}">${badgeText}</span>`;
        checkInTime.textContent = mockData.today.checkIn || '-';
        checkOutTime.textContent = mockData.today.checkOut || '-';

        if (mockData.today.checkIn && mockData.today.checkOut) {
            // Calculate hours
            const checkIn = new Date(`2025-12-04 ${mockData.today.checkIn}`);
            const checkOut = new Date(`2025-12-04 ${mockData.today.checkOut}`);
            const diffMs = checkOut - checkIn;
            const diffHours = diffMs / (1000 * 60 * 60);
            totalHours.textContent = diffHours.toFixed(1) + 'h';
        } else {
            totalHours.textContent = '-';
        }
    }

    // Update weekly/monthly summaries
    function updateSummaries() {
        document.getElementById('week-present').textContent = mockData.week.present;
        document.getElementById('week-absent').textContent = mockData.week.absent;
        document.getElementById('week-hours').textContent = mockData.week.totalHours.toFixed(1);

        document.getElementById('month-present').textContent = mockData.month.present;
        document.getElementById('month-absent').textContent = mockData.month.absent;
        document.getElementById('month-hours').textContent = mockData.month.totalHours.toFixed(1);
    }

    // Update recent activity
    function updateRecentActivity() {
        let html = '';

        if (mockData.recentActivity.length === 0) {
            html = `
                <div class="text-center text-gray-500 py-8">
                    <i class="ti ti-clock text-3xl mb-2"></i>
                    <p>No recent activity</p>
                </div>
            `;
        } else {
            mockData.recentActivity.forEach(activity => {
                const time = new Date(activity.time).toLocaleTimeString();
                const date = new Date(activity.time).toLocaleDateString();
                let icon = 'ti-clock';
                let color = 'text-gray-600';

                if (activity.type === 'check_in') {
                    icon = 'ti-login';
                    color = 'text-green-600';
                } else if (activity.type === 'check_out') {
                    icon = 'ti-logout';
                    color = 'text-red-600';
                } else if (activity.type.includes('break')) {
                    icon = 'ti-coffee';
                    color = 'text-orange-600';
                }

                const actionText = activity.type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

                html += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="ti ${icon} text-lg mr-3 ${color}"></i>
                            <div>
                                <p class="font-medium">${actionText}</p>
                                <p class="text-sm text-gray-600">${date} at ${time}</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            ${activity.location}
                        </div>
                    </div>
                `;
            });
        }

        recentActivity.innerHTML = html;
    }

    // Initialize
    function initialize() {
        updateTodayStatus();
        updateSummaries();
        updateRecentActivity();
    }

    // Simulate real-time updates (replace with actual API polling)
    setInterval(() => {
        // In real implementation, fetch updated status from API
        console.log('Checking for status updates...');
    }, 30000); // Check every 30 seconds

    initialize();
});
</script>

@endsection