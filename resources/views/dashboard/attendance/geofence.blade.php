@extends('layouts.app')
@section('title', 'Geofence Validation')
@section('content')

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">Geofence Validation</h1>
            <h5 class="text-xs text-gray-500">Validate your location for attendance</h5>
        </div>

        <a href="{{ route('dashboard.attendance.index') }}"
            class="bg-red-400 hover:bg-red-600 text-white px-4 py-2 rounded-md">
            <i class="ti ti-arrow-left mr-2"></i>
            Back to Attendance
        </a>
    </div>

    {{-- Current Location Status --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Location Status</h3>
                <p class="text-sm text-gray-600 mt-1" id="current-time">{{ now()->format('l, F j, Y \a\t g:i A') }}</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold mb-2" id="location-status">
                    <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-600">Checking...</span>
                </div>
                <p class="text-sm text-gray-600" id="location-message">Validating your location</p>
            </div>
        </div>
    </div>

    {{-- Location Information --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Your Location --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Current Location</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Latitude:</span>
                    <span id="user-latitude" class="font-mono">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Longitude:</span>
                    <span id="user-longitude" class="font-mono">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Accuracy:</span>
                    <span id="user-accuracy" class="font-mono">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Altitude:</span>
                    <span id="user-altitude" class="font-mono">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Last Updated:</span>
                    <span id="location-timestamp" class="text-sm">-</span>
                </div>
            </div>
        </div>

        {{-- Office Location --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Office Location</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Latitude:</span>
                    <span id="office-latitude" class="font-mono">12.9716</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Longitude:</span>
                    <span id="office-longitude" class="font-mono">77.5946</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Radius:</span>
                    <span id="office-radius" class="font-mono">100m</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Address:</span>
                    <span class="text-sm">Main Office Building</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Distance:</span>
                    <span id="distance-to-office" class="font-mono">-</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Validation Actions --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Location Validation</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button id="validate-location-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="ti ti-refresh mr-2"></i>
                Validate Location
            </button>
            <button id="check-geofence-btn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="ti ti-map-pin mr-2"></i>
                Check Geofence
            </button>
            <button id="refresh-location-btn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-3 rounded-lg">
                <i class="ti ti-reload mr-2"></i>
                Refresh Location
            </button>
        </div>

        {{-- Validation Result --}}
        <div id="validation-result" class="mt-6 p-4 rounded-lg hidden">
            <div class="flex items-center">
                <i id="result-icon" class="text-2xl mr-3"></i>
                <div>
                    <p id="result-title" class="font-medium"></p>
                    <p id="result-message" class="text-sm text-gray-600"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Location History --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Location Validations</h3>
        <div id="location-history" class="space-y-3">
            <div class="text-center text-gray-500 py-8">
                <i class="ti ti-history text-3xl mb-2"></i>
                <p>No recent validations</p>
            </div>
        </div>
    </div>

    {{-- Geofence Information --}}
    <div class="bg-blue-50 p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-blue-800 mb-4">Geofence Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-blue-700 mb-2">Geofence Rules</h4>
                <ul class="text-sm text-blue-600 space-y-1">
                    <li>• Must be within 100 meters of office location</li>
                    <li>• GPS accuracy should be within 50 meters</li>
                    <li>• Location must be validated before check-in/out</li>
                    <li>• Automatic validation occurs every 5 minutes</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium text-blue-700 mb-2">Troubleshooting</h4>
                <ul class="text-sm text-blue-600 space-y-1">
                    <li>• Ensure GPS/location services are enabled</li>
                    <li>• Check if you're in an area with good GPS signal</li>
                    <li>• Try refreshing your location</li>
                    <li>• Contact admin if issues persist</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for geofence validation --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const locationStatus = document.getElementById('location-status');
    const locationMessage = document.getElementById('location-message');
    const validateBtn = document.getElementById('validate-location-btn');
    const checkGeofenceBtn = document.getElementById('check-geofence-btn');
    const refreshBtn = document.getElementById('refresh-location-btn');
    const validationResult = document.getElementById('validation-result');
    const resultIcon = document.getElementById('result-icon');
    const resultTitle = document.getElementById('result-title');
    const resultMessage = document.getElementById('result-message');
    const locationHistory = document.getElementById('location-history');

    let currentLocation = null;
    let lastValidationTime = null;
    const officeLocation = { lat: 12.9716, lng: 77.5946 }; // Bangalore coordinates as example
    const geofenceRadius = 100; // meters

    // Mock location history
    const mockHistory = [
        {
            timestamp: '2025-12-04 09:15:00',
            status: 'valid',
            distance: 45,
            accuracy: 10
        },
        {
            timestamp: '2025-12-04 09:10:00',
            status: 'valid',
            distance: 52,
            accuracy: 8
        }
    ];

    // Calculate distance between two points using Haversine formula
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Earth's radius in meters
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c;
    }

    // Get user's location
    function getLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation is not supported'));
                return;
            }

            locationStatus.innerHTML = '<span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-600">Getting Location...</span>';
            locationMessage.textContent = 'Retrieving your current location';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                        accuracy: position.coords.accuracy,
                        altitude: position.coords.altitude || 0,
                        timestamp: new Date().toLocaleString()
                    };

                    // Update display
                    document.getElementById('user-latitude').textContent = currentLocation.lat.toFixed(6);
                    document.getElementById('user-longitude').textContent = currentLocation.lng.toFixed(6);
                    document.getElementById('user-accuracy').textContent = currentLocation.accuracy.toFixed(1) + 'm';
                    document.getElementById('user-altitude').textContent = currentLocation.altitude.toFixed(1) + 'm';
                    document.getElementById('location-timestamp').textContent = currentLocation.timestamp;

                    // Calculate distance to office
                    const distance = calculateDistance(
                        currentLocation.lat, currentLocation.lng,
                        officeLocation.lat, officeLocation.lng
                    );
                    document.getElementById('distance-to-office').textContent = distance.toFixed(1) + 'm';

                    // Update status
                    updateLocationStatus(distance);

                    resolve(currentLocation);
                },
                (error) => {
                    console.error('Location error:', error);
                    locationStatus.innerHTML = '<span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-600">Location Error</span>';
                    locationMessage.textContent = 'Unable to get your location. Please check GPS settings.';
                    reject(error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 300000 // 5 minutes
                }
            );
        });
    }

    // Update location status based on distance and accuracy
    function updateLocationStatus(distance) {
        const accuracy = currentLocation?.accuracy || 0;

        if (accuracy > 100) {
            locationStatus.innerHTML = '<span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-600">Poor Accuracy</span>';
            locationMessage.textContent = 'GPS accuracy is low. Try moving to an open area.';
        } else if (distance <= geofenceRadius) {
            locationStatus.innerHTML = '<span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-600">Within Geofence</span>';
            locationMessage.textContent = 'You are within the office geofence area.';
        } else {
            locationStatus.innerHTML = '<span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-600">Outside Geofence</span>';
            locationMessage.textContent = `You are ${distance.toFixed(1)}m away from the office.`;
        }
    }

    // Validate location
    async function validateLocation() {
        if (!currentLocation) {
            await getLocation();
        }

        validateBtn.disabled = true;
        validateBtn.innerHTML = '<i class="ti ti-loader animate-spin mr-2"></i> Validating...';

        try {
            const distance = calculateDistance(
                currentLocation.lat, currentLocation.lng,
                officeLocation.lat, officeLocation.lng
            );

            const isValid = distance <= geofenceRadius && currentLocation.accuracy <= 50;
            lastValidationTime = new Date();

            showValidationResult(isValid, distance, currentLocation.accuracy);

            // Add to history
            addToHistory({
                timestamp: lastValidationTime.toLocaleString(),
                status: isValid ? 'valid' : 'invalid',
                distance: distance,
                accuracy: currentLocation.accuracy
            });

            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 1500));

        } catch (error) {
            console.error('Validation error:', error);
            showValidationResult(false, 0, 0, 'Location validation failed');
        } finally {
            validateBtn.disabled = false;
            validateBtn.innerHTML = '<i class="ti ti-refresh mr-2"></i> Validate Location';
        }
    }

    // Check geofence specifically
    async function checkGeofence() {
        if (!currentLocation) {
            await getLocation();
        }

        checkGeofenceBtn.disabled = true;
        checkGeofenceBtn.innerHTML = '<i class="ti ti-loader animate-spin mr-2"></i> Checking...';

        try {
            const distance = calculateDistance(
                currentLocation.lat, currentLocation.lng,
                officeLocation.lat, officeLocation.lng
            );

            const inGeofence = distance <= geofenceRadius;

            showValidationResult(inGeofence, distance, currentLocation.accuracy,
                inGeofence ? 'You are within the geofence area' : `You are ${distance.toFixed(1)}m outside the geofence`);

            await new Promise(resolve => setTimeout(resolve, 1000));

        } catch (error) {
            console.error('Geofence check error:', error);
            showValidationResult(false, 0, 0, 'Geofence check failed');
        } finally {
            checkGeofenceBtn.disabled = false;
            checkGeofenceBtn.innerHTML = '<i class="ti ti-map-pin mr-2"></i> Check Geofence';
        }
    }

    // Show validation result
    function showValidationResult(isValid, distance, accuracy, customMessage = null) {
        validationResult.classList.remove('hidden');

        if (isValid) {
            validationResult.className = 'mt-6 p-4 rounded-lg bg-green-50';
            resultIcon.className = 'ti ti-check-circle text-2xl mr-3 text-green-600';
            resultTitle.textContent = 'Location Validated Successfully';
            resultMessage.textContent = customMessage || `You are within the allowed area (${distance.toFixed(1)}m from office, accuracy: ${accuracy.toFixed(1)}m)`;
        } else {
            validationResult.className = 'mt-6 p-4 rounded-lg bg-red-50';
            resultIcon.className = 'ti ti-alert-circle text-2xl mr-3 text-red-600';
            resultTitle.textContent = 'Location Validation Failed';
            resultMessage.textContent = customMessage || `You are outside the allowed area (${distance.toFixed(1)}m from office, accuracy: ${accuracy.toFixed(1)}m)`;
        }
    }

    // Add validation to history
    function addToHistory(validation) {
        const historyItem = document.createElement('div');
        historyItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';

        const statusClasses = {
            'valid': 'bg-green-100 text-green-600',
            'invalid': 'bg-red-100 text-red-600'
        };

        historyItem.innerHTML = `
            <div class="flex items-center">
                <i class="ti ti-${validation.status === 'valid' ? 'check-circle' : 'x-circle'} text-lg mr-3 ${validation.status === 'valid' ? 'text-green-600' : 'text-red-600'}"></i>
                <div>
                    <p class="font-medium">${validation.timestamp}</p>
                    <p class="text-sm text-gray-600">Distance: ${validation.distance.toFixed(1)}m, Accuracy: ${validation.accuracy.toFixed(1)}m</p>
                </div>
            </div>
            <span class="px-2 py-1 text-xs rounded-full ${statusClasses[validation.status] || 'bg-gray-100 text-gray-600'}">${validation.status.charAt(0).toUpperCase() + validation.status.slice(1)}</span>
        `;

        // Remove "no validations" message if it exists
        const noValidations = locationHistory.querySelector('.text-center');
        if (noValidations) {
            locationHistory.innerHTML = '';
        }

        locationHistory.insertBefore(historyItem, locationHistory.firstChild);

        // Keep only last 10 items
        const items = locationHistory.children;
        if (items.length > 10) {
            locationHistory.removeChild(items[items.length - 1]);
        }
    }

    // Render initial history
    function renderHistory() {
        mockHistory.forEach(item => addToHistory(item));
    }

    // Initialize
    async function initialize() {
        renderHistory();

        try {
            await getLocation();
        } catch (error) {
            console.warn('Initial location fetch failed:', error);
        }
    }

    // Update current time every minute
    setInterval(() => {
        document.getElementById('current-time').textContent = new Date().toLocaleDateString() + ' at ' + new Date().toLocaleTimeString();
    }, 60000);

    // Event listeners
    validateBtn.addEventListener('click', validateLocation);
    checkGeofenceBtn.addEventListener('click', checkGeofence);
    refreshBtn.addEventListener('click', getLocation);

    // Initialize
    initialize();
});
</script>

@endsection