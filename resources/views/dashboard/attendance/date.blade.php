@extends('layouts.app')
@section('title', 'View Attendance by Date')
@section('content')

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">View Attendance by Date</h1>
            <h5 class="text-xs text-gray-500">View detailed attendance records for a specific date</h5>
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
            <input type="date" id="view-date" value="{{ date('Y-m-d') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400">
            <button id="view-attendance-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i class="ti ti-search mr-2"></i>
                View Attendance
            </button>
            <button id="export-btn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                <i class="ti ti-download mr-2"></i>
                Export
            </button>
        </div>
    </div>

    {{-- Date Summary Cards --}}
    <div id="date-summary" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6" style="display: none;">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Total Staff</h3>
                    <p class="text-3xl font-bold text-blue-600" id="date-total-staff">0</p>
                </div>
                <i class="ti ti-users text-4xl text-blue-500"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Present</h3>
                    <p class="text-3xl font-bold text-green-600" id="date-present">0</p>
                </div>
                <i class="ti ti-user-check text-4xl text-green-500"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Absent</h3>
                    <p class="text-3xl font-bold text-red-600" id="date-absent">0</p>
                </div>
                <i class="ti ti-user-x text-4xl text-red-500"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Late Arrivals</h3>
                    <p class="text-3xl font-bold text-yellow-600" id="date-late">0</p>
                </div>
                <i class="ti ti-clock text-4xl text-yellow-500"></i>
            </div>
        </div>
    </div>

    {{-- Attendance Details Table --}}
    <div class="bg-white shadow-md sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4" id="table-title">Select a date to view attendance records</h3>
        <div class="overflow-x-auto">
            <table id="date-attendance-table" class="table bg-white table-bordered w-full" style="display: none;">
                <thead class="text-xs text-gray-700 uppercase bg-gray-800 text-white">
                    <tr class="text-center">
                        <th class="px-6 py-3">Staff Member</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Check In</th>
                        <th class="px-6 py-3">Check Out</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Working Hours</th>
                        <th class="px-6 py-3">Late By</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody id="date-attendance-rows">
                    {{-- Attendance records will be loaded here --}}
                </tbody>
            </table>

            {{-- No data message --}}
            <div id="no-data-message" class="text-center py-8 text-gray-500">
                <i class="ti ti-calendar-x text-4xl mb-4"></i>
                <p class="text-lg">No attendance records found for the selected date</p>
                <p class="text-sm">Try selecting a different date or check if attendance has been marked</p>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for date view --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewDate = document.getElementById('view-date');
    const viewBtn = document.getElementById('view-attendance-btn');
    const exportBtn = document.getElementById('export-btn');
    const table = document.getElementById('date-attendance-table');
    const tableTitle = document.getElementById('table-title');
    const summaryCards = document.getElementById('date-summary');
    const noDataMessage = document.getElementById('no-data-message');
    const attendanceRows = document.getElementById('date-attendance-rows');

    // View attendance for selected date
    function viewAttendance() {
        const date = viewDate.value;
        const formattedDate = new Date(date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        tableTitle.textContent = `Attendance Records for ${formattedDate}`;

        // Show loading state
        viewBtn.innerHTML = '<i class="ti ti-loader animate-spin mr-2"></i> Loading...';
        viewBtn.disabled = true;

        // Simulate loading attendance data (replace with actual API call)
        setTimeout(() => {
            loadAttendanceData(date, formattedDate);
        }, 1500);
    }

    function loadAttendanceData(date, formattedDate) {
        // Mock data - replace with actual API call
        const mockAttendance = [
            {
                id: 1,
                name: 'John Doe',
                role: 'Tailor',
                profile_pic: 'https://avatar.iran.liara.run/public',
                check_in: '09:15 AM',
                check_out: '05:45 PM',
                status: 'late',
                working_hours: '8.5',
                late_by: '15 mins'
            },
            {
                id: 2,
                name: 'Jane Smith',
                role: 'Master',
                profile_pic: 'https://avatar.iran.liara.run/public',
                check_in: '08:50 AM',
                check_out: '05:30 PM',
                status: 'present',
                working_hours: '8.67',
                late_by: '-'
            },
            {
                id: 3,
                name: 'Bob Johnson',
                profile_pic: 'https://avatar.iran.liara.run/public',
                role: 'Stitcher',
                check_in: '-',
                check_out: '-',
                status: 'absent',
                working_hours: '0',
                late_by: '-'
            }
        ];

        // Update summary cards
        const totalStaff = mockAttendance.length;
        const present = mockAttendance.filter(a => a.status === 'present' || a.status === 'late').length;
        const absent = mockAttendance.filter(a => a.status === 'absent').length;
        const late = mockAttendance.filter(a => a.status === 'late').length;

        document.getElementById('date-total-staff').textContent = totalStaff;
        document.getElementById('date-present').textContent = present;
        document.getElementById('date-absent').textContent = absent;
        document.getElementById('date-late').textContent = late;

        // Generate table rows
        let html = '';
        mockAttendance.forEach(attendance => {
            const statusClass = {
                'present': 'bg-green-100 text-green-600',
                'late': 'bg-yellow-100 text-yellow-600',
                'absent': 'bg-red-100 text-red-600'
            }[attendance.status] || 'bg-gray-100 text-gray-600';

            const statusText = {
                'present': 'Present',
                'late': 'Late',
                'absent': 'Absent'
            }[attendance.status] || 'Unknown';

            html += `
                <tr class="bg-white border-b text-center">
                    <td class="px-6 py-4 flex items-center justify-center">
                        <img class="w-8 h-8 p-1 rounded-full ring-1 ring-gray-300 mr-2" src="${attendance.profile_pic}" alt="Avatar">
                        <span>${attendance.name}</span>
                    </td>
                    <td class="px-6 py-4">${attendance.role}</td>
                    <td class="px-6 py-4">${attendance.check_in}</td>
                    <td class="px-6 py-4">${attendance.check_out}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full ${statusClass}">${statusText}</span>
                    </td>
                    <td class="px-6 py-4">${attendance.working_hours}h</td>
                    <td class="px-6 py-4">${attendance.late_by}</td>
                    <td class="px-6 py-4">
                        <button class="text-blue-500 hover:text-blue-700 mr-2" onclick="editAttendance(${attendance.id})">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="text-green-500 hover:text-green-700" onclick="viewDetails(${attendance.id})">
                            <i class="ti ti-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        attendanceRows.innerHTML = html;
        summaryCards.style.display = 'grid';
        table.style.display = 'table';
        noDataMessage.style.display = 'none';

        // Reset button
        viewBtn.innerHTML = '<i class="ti ti-search mr-2"></i> View Attendance';
        viewBtn.disabled = false;
    }

    // Export functionality
    exportBtn.addEventListener('click', function() {
        alert('Export functionality would be implemented here');
    });

    // Event listeners
    viewBtn.addEventListener('click', viewAttendance);
    viewDate.addEventListener('change', function() {
        summaryCards.style.display = 'none';
        table.style.display = 'none';
        noDataMessage.style.display = 'block';
    });
});

// Placeholder functions for actions
function editAttendance(id) {
    alert(`Edit attendance for staff ID: ${id}`);
}

function viewDetails(id) {
    alert(`View details for staff ID: ${id}`);
}
</script>

@endsection