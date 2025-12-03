@extends('layouts.app')
@section('title', 'Mark Attendance')
@section('content')

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">Mark Attendance</h1>
            <h5 class="text-xs text-gray-500">Record daily attendance for staff members</h5>
        </div>

        <a href="{{ route('dashboard.attendance.index') }}"
            class="bg-red-400 hover:bg-red-600 text-white px-4 py-2 rounded-md">
            <i class="ti ti-arrow-left mr-2"></i>
            Back to Attendance
        </a>
    </div>

    {{-- Date Selector --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-600">Select Date:</label>
            <input type="date" id="attendance-date" value="{{ date('Y-m-d') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400">
            <button id="load-staff-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i class="ti ti-refresh mr-2"></i>
                Load Staff
            </button>
        </div>
    </div>

    {{-- Staff Attendance Form --}}
    <div class="bg-white shadow-md sm:rounded-lg p-6">
        <form id="attendance-form">
            @csrf
            <input type="hidden" id="selected-date" name="date" value="{{ date('Y-m-d') }}">

            <div class="overflow-x-auto">
                <table class="table bg-white table-bordered w-full">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-800 text-white">
                        <tr class="text-center">
                            <th class="px-6 py-3">Staff Member</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Check In Time</th>
                            <th class="px-6 py-3">Check Out Time</th>
                            <th class="px-6 py-3">Notes</th>
                        </tr>
                    </thead>
                    <tbody id="staff-attendance-rows">
                        {{-- Staff rows will be loaded here via JavaScript --}}
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="ti ti-loader text-2xl mb-2"></i>
                                <p>Select a date and click "Load Staff" to begin marking attendance</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end mt-6">
                <button type="submit" id="save-attendance-btn"
                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="ti ti-device-floppy mr-2"></i>
                    Save Attendance
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript for attendance marking --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadStaffBtn = document.getElementById('load-staff-btn');
    const attendanceDate = document.getElementById('attendance-date');
    const selectedDate = document.getElementById('selected-date');
    const staffRows = document.getElementById('staff-attendance-rows');
    const saveBtn = document.getElementById('save-attendance-btn');

    // Load staff when date changes or button clicked
    function loadStaff() {
        const date = attendanceDate.value;
        selectedDate.value = date;

        // Show loading
        staffRows.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center">
                    <i class="ti ti-loader animate-spin text-2xl mb-2"></i>
                    <p>Loading staff...</p>
                </td>
            </tr>
        `;

        // Simulate loading staff (replace with actual API call)
        setTimeout(() => {
            loadStaffData(date);
        }, 1000);
    }

    function loadStaffData(date) {
        // Mock data - replace with actual API call
        const mockStaff = [
            { id: 1, name: 'John Doe', role: 'Tailor', profile_pic: 'https://avatar.iran.liara.run/public' },
            { id: 2, name: 'Jane Smith', role: 'Master', profile_pic: 'https://avatar.iran.liara.run/public' },
            { id: 3, name: 'Bob Johnson', role: 'Stitcher', profile_pic: 'https://avatar.iran.liara.run/public' }
        ];

        let html = '';
        mockStaff.forEach(staff => {
            html += `
                <tr class="bg-white border-b">
                    <td class="px-6 py-4 flex items-center">
                        <img class="w-10 h-10 p-1 rounded-full ring-1 ring-gray-300 mr-3" src="${staff.profile_pic}" alt="Avatar">
                        <span>${staff.name}</span>
                    </td>
                    <td class="px-6 py-4 text-center">${staff.role}</td>
                    <td class="px-6 py-4 text-center">
                        <select name="status[${staff.id}]" class="border border-gray-300 rounded px-2 py-1">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                            <option value="leave">Leave</option>
                        </select>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <input type="time" name="check_in[${staff.id}]" class="border border-gray-300 rounded px-2 py-1">
                    </td>
                    <td class="px-6 py-4 text-center">
                        <input type="time" name="check_out[${staff.id}]" class="border border-gray-300 rounded px-2 py-1">
                    </td>
                    <td class="px-6 py-4 text-center">
                        <input type="text" name="notes[${staff.id}]" placeholder="Optional notes"
                            class="border border-gray-300 rounded px-2 py-1 w-full">
                    </td>
                </tr>
            `;
        });

        staffRows.innerHTML = html;
        saveBtn.disabled = false;
    }

    // Event listeners
    loadStaffBtn.addEventListener('click', loadStaff);
    attendanceDate.addEventListener('change', function() {
        selectedDate.value = this.value;
    });

    // Form submission
    document.getElementById('attendance-form').addEventListener('submit', function(e) {
        e.preventDefault();

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="ti ti-loader animate-spin mr-2"></i> Saving...';

        // Simulate save (replace with actual API call)
        setTimeout(() => {
            alert('Attendance saved successfully!');
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="ti ti-device-floppy mr-2"></i> Save Attendance';
        }, 2000);
    });
});
</script>

@endsection