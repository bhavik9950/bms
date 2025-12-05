@extends('layouts.app')
@section('title', 'Update Attendance Status')
@section('content')

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">Update Attendance Status</h1>
            <h5 class="text-xs text-gray-500">Modify your attendance records</h5>
        </div>

        <a href="{{ route('dashboard.attendance.index') }}"
            class="bg-red-400 hover:bg-red-600 text-white px-4 py-2 rounded-md">
            <i class="ti ti-arrow-left mr-2"></i>
            Back to Attendance
        </a>
    </div>

    {{-- Date Selection --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex items-center gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Date</label>
                <input type="date" id="update-date" value="{{ now()->format('Y-m-d') }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400">
            </div>
            <button id="load-record-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i class="ti ti-search mr-2"></i>
                Load Record
            </button>
        </div>
    </div>

    {{-- Current Record Display --}}
    <div id="current-record" class="bg-white p-6 rounded-lg shadow mb-6 hidden">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Attendance Record</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <label class="block text-sm font-medium text-gray-700 mb-1">Check In Time</label>
                <p id="current-check-in" class="text-lg font-semibold">-</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <label class="block text-sm font-medium text-gray-700 mb-1">Check Out Time</label>
                <p id="current-check-out" class="text-lg font-semibold">-</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <p id="current-status" class="text-lg font-semibold">-</p>
            </div>
        </div>
    </div>

    {{-- Update Form --}}
    <div id="update-form" class="bg-white p-6 rounded-lg shadow hidden">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Attendance</h3>
        <form id="attendance-update-form">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Check In Time --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check In Time</label>
                    <input type="time" id="check-in-time" name="check_in_time"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400">
                    <p class="text-xs text-gray-500 mt-1">Leave empty if not changing</p>
                </div>

                {{-- Check Out Time --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check Out Time</label>
                    <input type="time" id="check-out-time" name="check_out_time"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400">
                    <p class="text-xs text-gray-500 mt-1">Leave empty if not changing</p>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="attendance-status" name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400">
                        <option value="">Select Status</option>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="half_day">Half Day</option>
                        <option value="leave">Leave</option>
                    </select>
                </div>

                {{-- Reason --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Update</label>
                    <select id="update-reason" name="update_reason" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400">
                        <option value="">Select Reason</option>
                        <option value="forgot_check_in">Forgot to check in</option>
                        <option value="forgot_check_out">Forgot to check out</option>
                        <option value="wrong_time">Wrong time entry</option>
                        <option value="system_error">System error</option>
                        <option value="location_issue">Location tracking issue</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>

            {{-- Additional Notes --}}
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                <textarea id="additional-notes" name="notes" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400"
                    placeholder="Provide any additional details about this update..."></textarea>
            </div>

            {{-- Location Information --}}
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h4 class="text-sm font-medium text-blue-800 mb-2">Location Verification</h4>
                <p class="text-sm text-blue-700 mb-2">Your current location will be recorded with this update for verification purposes.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-blue-600">Latitude:</span>
                        <span id="update-latitude" class="font-mono">-</span>
                    </div>
                    <div>
                        <span class="text-blue-600">Longitude:</span>
                        <span id="update-longitude" class="font-mono">-</span>
                    </div>
                    <div>
                        <span class="text-blue-600">Accuracy:</span>
                        <span id="update-accuracy" class="font-mono">-</span>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end mt-6">
                <button type="button" id="cancel-btn"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg mr-3">
                    Cancel
                </button>
                <button type="submit" id="update-btn"
                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="ti ti-device-floppy mr-2"></i>
                    Update Attendance
                </button>
            </div>
        </form>
    </div>

    {{-- Update History --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Updates</h3>
        <div id="update-history" class="space-y-3">
            <div class="text-center text-gray-500 py-8">
                <i class="ti ti-history text-3xl mb-2"></i>
                <p>No recent updates</p>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for status updates --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadRecordBtn = document.getElementById('load-record-btn');
    const updateDate = document.getElementById('update-date');
    const currentRecord = document.getElementById('current-record');
    const updateForm = document.getElementById('update-form');
    const updateFormEl = document.getElementById('attendance-update-form');
    const cancelBtn = document.getElementById('cancel-btn');
    const updateBtn = document.getElementById('update-btn');
    const updateHistory = document.getElementById('update-history');

    let currentAttendanceData = null;

    // Mock data - replace with actual API calls
    const mockAttendanceData = {
        '2025-12-04': {
            checkIn: '09:15 AM',
            checkOut: '06:30 PM',
            status: 'present',
            location: 'Main Office'
        },
        '2025-12-03': {
            checkIn: '09:45 AM',
            checkOut: null,
            status: 'present',
            location: 'Main Office'
        }
    };

    const mockUpdateHistory = [
        {
            date: '2025-12-03',
            time: '10:30 AM',
            changes: 'Updated check-out time',
            reason: 'Forgot to check out',
            status: 'approved'
        },
        {
            date: '2025-12-01',
            time: '09:15 AM',
            changes: 'Corrected check-in time',
            reason: 'Wrong time entry',
            status: 'pending'
        }
    ];

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
                    document.getElementById('update-latitude').textContent = location.latitude.toFixed(6);
                    document.getElementById('update-longitude').textContent = location.longitude.toFixed(6);
                    document.getElementById('update-accuracy').textContent = location.accuracy.toFixed(1) + 'm';

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

    // Load attendance record for selected date
    function loadRecord() {
        const date = updateDate.value;
        currentAttendanceData = mockAttendanceData[date];

        if (currentAttendanceData) {
            // Show current record
            document.getElementById('current-check-in').textContent = currentAttendanceData.checkIn || '-';
            document.getElementById('current-check-out').textContent = currentAttendanceData.checkOut || '-';
            document.getElementById('current-status').textContent = currentAttendanceData.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

            // Pre-fill form with current values
            document.getElementById('check-in-time').value = currentAttendanceData.checkIn ? convertTo24Hour(currentAttendanceData.checkIn) : '';
            document.getElementById('check-out-time').value = currentAttendanceData.checkOut ? convertTo24Hour(currentAttendanceData.checkOut) : '';
            document.getElementById('attendance-status').value = currentAttendanceData.status;

            currentRecord.classList.remove('hidden');
            updateForm.classList.remove('hidden');

            // Get location for verification
            getLocation().catch(error => {
                console.warn('Location fetch failed:', error);
            });
        } else {
            alert('No attendance record found for the selected date.');
            currentRecord.classList.add('hidden');
            updateForm.classList.add('hidden');
        }
    }

    // Convert 12-hour time to 24-hour format
    function convertTo24Hour(time12h) {
        if (!time12h) return '';
        const [time, modifier] = time12h.split(' ');
        let [hours, minutes] = time.split(':');
        hours = parseInt(hours);

        if (modifier === 'PM' && hours !== 12) {
            hours += 12;
        } else if (modifier === 'AM' && hours === 12) {
            hours = 0;
        }

        return `${hours.toString().padStart(2, '0')}:${minutes}`;
    }

    // Submit update form
    async function submitUpdate(e) {
        e.preventDefault();

        updateBtn.disabled = true;
        updateBtn.innerHTML = '<i class="ti ti-loader animate-spin mr-2"></i> Updating...';

        try {
            // Get location for verification
            const location = await getLocation();

            const formData = new FormData(updateFormEl);
            const updateData = {
                date: updateDate.value,
                check_in_time: formData.get('check_in_time'),
                check_out_time: formData.get('check_out_time'),
                status: formData.get('status'),
                update_reason: formData.get('update_reason'),
                notes: formData.get('notes'),
                location: location
            };

            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 2000));

            // Add to update history
            const newUpdate = {
                date: updateData.date,
                time: new Date().toLocaleTimeString(),
                changes: 'Attendance updated',
                reason: updateData.update_reason.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()),
                status: 'pending'
            };
            mockUpdateHistory.unshift(newUpdate);
            renderUpdateHistory();

            alert('Attendance update request submitted successfully! It will be reviewed by your supervisor.');

            // Reset form
            updateFormEl.reset();
            currentRecord.classList.add('hidden');
            updateForm.classList.add('hidden');

        } catch (error) {
            console.error('Update failed:', error);
            alert('Failed to update attendance. Please ensure location services are enabled.');
        } finally {
            updateBtn.disabled = false;
            updateBtn.innerHTML = '<i class="ti ti-device-floppy mr-2"></i> Update Attendance';
        }
    }

    // Render update history
    function renderUpdateHistory() {
        let html = '';

        if (mockUpdateHistory.length === 0) {
            html = `
                <div class="text-center text-gray-500 py-8">
                    <i class="ti ti-history text-3xl mb-2"></i>
                    <p>No recent updates</p>
                </div>
            `;
        } else {
            mockUpdateHistory.forEach(update => {
                const statusClasses = {
                    'approved': 'bg-green-100 text-green-600',
                    'pending': 'bg-yellow-100 text-yellow-600',
                    'rejected': 'bg-red-100 text-red-600'
                };

                html += `
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="ti ti-edit text-lg mr-3 text-blue-600"></i>
                            <div>
                                <p class="font-medium">${update.changes}</p>
                                <p class="text-sm text-gray-600">${update.date} at ${update.time}</p>
                                <p class="text-sm text-gray-600">Reason: ${update.reason}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full ${statusClasses[update.status] || 'bg-gray-100 text-gray-600'}">${update.status.charAt(0).toUpperCase() + update.status.slice(1)}</span>
                    </div>
                `;
            });
        }

        updateHistory.innerHTML = html;
    }

    // Cancel update
    function cancelUpdate() {
        if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
            updateFormEl.reset();
            currentRecord.classList.add('hidden');
            updateForm.classList.add('hidden');
        }
    }

    // Event listeners
    loadRecordBtn.addEventListener('click', loadRecord);
    updateFormEl.addEventListener('submit', submitUpdate);
    cancelBtn.addEventListener('click', cancelUpdate);

    // Initialize
    renderUpdateHistory();
});
</script>

@endsection