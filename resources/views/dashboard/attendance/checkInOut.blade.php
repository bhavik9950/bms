@extends('layouts.app')
@section('title', 'Check In/Out')
@section('content')

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">Check In/Out</h1>
            <h5 class="text-xs text-gray-500">Mark your attendance with location tracking</h5>
        </div>

        <a href="{{ route('dashboard.attendance.index') }}"
            class="bg-red-400 hover:bg-red-600 text-white px-4 py-2 rounded-md">
            <i class="ti ti-arrow-left mr-2"></i>
            Back to Attendance
        </a>
    </div>

    {{-- Current Status Card --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Current Status</h3>
                <p class="text-sm text-gray-600 mt-1" id="current-time">{{ now()->format('l, F j, Y \a\t g:i A') }}</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold" id="status-badge">
                    <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-600">Not Checked In</span>
                </div>
                <p class="text-sm text-gray-600 mt-1" id="last-action">No recent activity</p>
            </div>
        </div>
    </div>

    {{-- Check In/Out Button --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="text-center">
            <button id="check-in-out-btn" class="bg-green-500 hover:bg-green-600 text-white px-8 py-4 rounded-lg text-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="ti ti-login mr-2"></i>
                <span id="btn-text">Check In</span>
            </button>
            <p class="text-sm text-gray-600 mt-3">Your location will be recorded automatically</p>
        </div>

        {{-- Location Info --}}
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Location Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Latitude:</span>
                    <span id="latitude" class="font-mono">-</span>
                </div>
                <div>
                    <span class="text-gray-600">Longitude:</span>
                    <span id="longitude" class="font-mono">-</span>
                </div>
                <div>
                    <span class="text-gray-600">Accuracy:</span>
                    <span id="accuracy" class="font-mono">-</span>
                </div>
                <div>
                    <span class="text-gray-600">Battery:</span>
                    <span id="battery" class="font-mono">-</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Activity --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Today's Activity</h3>
        <div id="activity-log" class="space-y-3">
            <div class="text-center text-gray-500 py-8">
                <i class="ti ti-clock text-3xl mb-2"></i>
                <p>No activity recorded today</p>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for location tracking and check-in/out --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkBtn = document.getElementById('check-in-out-btn');
    const btnText = document.getElementById('btn-text');
    const statusBadge = document.getElementById('status-badge');
    const lastAction = document.getElementById('last-action');
    const activityLog = document.getElementById('activity-log');

    let currentLocation = null;
    let isCheckedIn = false;
    let checkInTime = null;

    // Get user's location
    function getLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation is not supported'));
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const location = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    };

                    // Update location display
                    document.getElementById('latitude').textContent = location.latitude.toFixed(6);
                    document.getElementById('longitude').textContent = location.longitude.toFixed(6);
                    document.getElementById('accuracy').textContent = location.accuracy.toFixed(1) + 'm';

                    resolve(location);
                },
                (error) => {
                    reject(error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000 // 5 minutes
                }
            );
        });
    }

    // Get battery level
    function getBatteryLevel() {
        return new Promise((resolve) => {
            if ('getBattery' in navigator) {
                navigator.getBattery().then(battery => {
                    const level = Math.round(battery.level * 100);
                    document.getElementById('battery').textContent = level + '%';
                    resolve(level);
                });
            } else {
                document.getElementById('battery').textContent = 'N/A';
                resolve(null);
            }
        });
    }

    // Update status display
    function updateStatusDisplay() {
        if (isCheckedIn) {
            statusBadge.innerHTML = '<span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-600">Checked In</span>';
            btnText.textContent = 'Check Out';
            checkBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
            checkBtn.classList.add('bg-red-500', 'hover:bg-red-600');
            lastAction.textContent = `Checked in at ${checkInTime}`;
        } else {
            statusBadge.innerHTML = '<span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-600">Not Checked In</span>';
            btnText.textContent = 'Check In';
            checkBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
            checkBtn.classList.add('bg-green-500', 'hover:bg-green-600');
            lastAction.textContent = checkInTime ? `Checked out at ${new Date().toLocaleTimeString()}` : 'No recent activity';
        }
    }

    // Add activity to log
    function addActivityLog(action, time, location) {
        const activityItem = document.createElement('div');
        activityItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
        activityItem.innerHTML = `
            <div class="flex items-center">
                <i class="ti ti-${action === 'check-in' ? 'login' : 'logout'} text-lg mr-3 ${action === 'check-in' ? 'text-green-600' : 'text-red-600'}"></i>
                <div>
                    <p class="font-medium">${action === 'check-in' ? 'Checked In' : 'Checked Out'}</p>
                    <p class="text-sm text-gray-600">${time}</p>
                </div>
            </div>
            <div class="text-right text-sm text-gray-600">
                <p>Lat: ${location.latitude.toFixed(4)}</p>
                <p>Lng: ${location.longitude.toFixed(4)}</p>
            </div>
        `;

        // Remove "no activity" message if it exists
        const noActivity = activityLog.querySelector('.text-center');
        if (noActivity) {
            activityLog.innerHTML = '';
        }

        activityLog.insertBefore(activityItem, activityLog.firstChild);
    }

    // Check in/out action
    async function performCheckInOut() {
        checkBtn.disabled = true;
        checkBtn.innerHTML = '<i class="ti ti-loader animate-spin mr-2"></i> Processing...';

        try {
            // Get location and battery
            const location = await getLocation();
            const battery = await getBatteryLevel();

            const action = isCheckedIn ? 'check-out' : 'check-in';
            const now = new Date();
            const timeString = now.toLocaleTimeString();

            // Simulate API call (replace with actual API)
            await new Promise(resolve => setTimeout(resolve, 2000));

            // Update state
            if (!isCheckedIn) {
                checkInTime = timeString;
                isCheckedIn = true;
            } else {
                isCheckedIn = false;
            }

            // Update display
            updateStatusDisplay();
            addActivityLog(action, timeString, location);

            // Show success message
            alert(`${action === 'check-in' ? 'Checked in' : 'Checked out'} successfully!`);

        } catch (error) {
            console.error('Error:', error);
            alert('Failed to ' + (isCheckedIn ? 'check out' : 'check in') + '. Please ensure location services are enabled.');
        } finally {
            checkBtn.disabled = false;
            checkBtn.innerHTML = `<i class="ti ti-${isCheckedIn ? 'logout' : 'login'} mr-2"></i> <span id="btn-text">${isCheckedIn ? 'Check Out' : 'Check In'}</span>`;
        }
    }

    // Initialize
    async function initialize() {
        try {
            await getLocation();
            await getBatteryLevel();
        } catch (error) {
            console.warn('Initial location/battery fetch failed:', error);
        }
        updateStatusDisplay();
    }

    // Event listeners
    checkBtn.addEventListener('click', performCheckInOut);

    // Update current time every minute
    setInterval(() => {
        document.getElementById('current-time').textContent = new Date().toLocaleDateString() + ' at ' + new Date().toLocaleTimeString();
    }, 60000);

    // Initialize on load
    initialize();
});
</script>

@endsection