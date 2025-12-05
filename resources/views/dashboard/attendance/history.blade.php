@extends('layouts.app')
@section('title', 'Attendance History')
@section('content')

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">Attendance History</h1>
            <h5 class="text-xs text-gray-500">View your past attendance records</h5>
        </div>

        <a href="{{ route('dashboard.attendance.index') }}"
            class="bg-red-400 hover:bg-red-600 text-white px-4 py-2 rounded-md">
            <i class="ti ti-arrow-left mr-2"></i>
            Back to Attendance
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" id="from-date" value="{{ now()->subDays(30)->format('Y-m-d') }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" id="to-date" value="{{ now()->format('Y-m-d') }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status-filter" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400">
                    <option value="">All Status</option>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="late">Late</option>
                    <option value="half_day">Half Day</option>
                    <option value="leave">Leave</option>
                </select>
            </div>
            <button id="filter-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i class="ti ti-filter mr-2"></i>
                Apply Filters
            </button>
            <button id="export-btn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                <i class="ti ti-download mr-2"></i>
                Export
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Total Days</h3>
                    <p class="text-2xl font-bold text-gray-800" id="total-days">0</p>
                </div>
                <i class="ti ti-calendar text-2xl text-blue-500"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Present Days</h3>
                    <p class="text-2xl font-bold text-green-600" id="present-days">0</p>
                </div>
                <i class="ti ti-user-check text-2xl text-green-500"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Absent Days</h3>
                    <p class="text-2xl font-bold text-red-600" id="absent-days">0</p>
                </div>
                <i class="ti ti-user-x text-2xl text-red-500"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Total Hours</h3>
                    <p class="text-2xl font-bold text-purple-600" id="total-hours">0.0</p>
                </div>
                <i class="ti ti-clock text-2xl text-purple-500"></i>
            </div>
        </div>
    </div>

    {{-- Attendance History Table --}}
    <div class="bg-white shadow-md sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Attendance Records</h3>
            <div class="overflow-x-auto">
                <table id="history-table" class="table bg-white table-bordered w-full">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-800 text-white">
                        <tr class="text-center">
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Day</th>
                            <th class="px-6 py-3">Check In</th>
                            <th class="px-6 py-3">Check Out</th>
                            <th class="px-6 py-3">Total Hours</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Location</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="history-rows">
                        {{-- History rows will be loaded here --}}
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="ti ti-loader text-2xl mb-2"></i>
                                <p>Loading attendance history...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="flex justify-between items-center mt-4">
                <div class="text-sm text-gray-600">
                    Showing <span id="showing-from">0</span> to <span id="showing-to">0</span> of <span id="total-records">0</span> entries
                </div>
                <div class="flex space-x-2" id="pagination">
                    <button id="prev-btn" class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">Previous</button>
                    <button id="next-btn" class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal for attendance details --}}
<div id="details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Attendance Details</h3>
            <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                <i class="ti ti-x text-xl"></i>
            </button>
        </div>
        <div id="modal-content">
            {{-- Modal content will be loaded here --}}
        </div>
    </div>
</div>

{{-- JavaScript for history management --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const historyRows = document.getElementById('history-rows');
    const filterBtn = document.getElementById('filter-btn');
    const exportBtn = document.getElementById('export-btn');
    const detailsModal = document.getElementById('details-modal');
    const closeModal = document.getElementById('close-modal');
    const modalContent = document.getElementById('modal-content');

    let currentPage = 1;
    let totalRecords = 0;
    const recordsPerPage = 10;

    // Mock data - replace with actual API calls
    const mockHistory = [
        {
            date: '2025-12-04',
            day: 'Wednesday',
            checkIn: '09:15 AM',
            checkOut: '06:30 PM',
            totalHours: 8.5,
            status: 'present',
            location: 'Main Office',
            breaks: [
                { start: '12:30 PM', end: '01:00 PM', duration: 0.5 }
            ],
            notes: 'On time'
        },
        {
            date: '2025-12-03',
            day: 'Tuesday',
            checkIn: '09:45 AM',
            checkOut: '06:15 PM',
            totalHours: 7.5,
            status: 'late',
            location: 'Main Office',
            breaks: [],
            notes: 'Late arrival - traffic'
        },
        {
            date: '2025-12-02',
            day: 'Monday',
            checkIn: '09:00 AM',
            checkOut: '05:30 PM',
            totalHours: 8.0,
            status: 'present',
            location: 'Main Office',
            breaks: [
                { start: '01:00 PM', end: '01:30 PM', duration: 0.5 }
            ],
            notes: ''
        }
    ];

    // Calculate summary stats
    function calculateSummary(data) {
        const total = data.length;
        const present = data.filter(item => item.status === 'present').length;
        const absent = data.filter(item => item.status === 'absent').length;
        const totalHours = data.reduce((sum, item) => sum + (item.totalHours || 0), 0);

        document.getElementById('total-days').textContent = total;
        document.getElementById('present-days').textContent = present;
        document.getElementById('absent-days').textContent = absent;
        document.getElementById('total-hours').textContent = totalHours.toFixed(1);
    }

    // Render history table
    function renderHistory(data, page = 1) {
        const start = (page - 1) * recordsPerPage;
        const end = start + recordsPerPage;
        const pageData = data.slice(start, end);

        let html = '';

        if (pageData.length === 0) {
            html = `
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        <i class="ti ti-calendar-x text-2xl mb-2"></i>
                        <p>No attendance records found</p>
                    </td>
                </tr>
            `;
        } else {
            pageData.forEach((record, index) => {
                const statusClasses = {
                    'present': 'bg-green-100 text-green-600',
                    'absent': 'bg-red-100 text-red-600',
                    'late': 'bg-yellow-100 text-yellow-600',
                    'half_day': 'bg-orange-100 text-orange-600',
                    'leave': 'bg-gray-100 text-gray-600'
                };

                const statusText = record.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

                html += `
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-center font-medium">${record.date}</td>
                        <td class="px-6 py-4 text-center">${record.day}</td>
                        <td class="px-6 py-4 text-center">${record.checkIn || '-'}</td>
                        <td class="px-6 py-4 text-center">${record.checkOut || '-'}</td>
                        <td class="px-6 py-4 text-center">${record.totalHours ? record.totalHours.toFixed(1) + 'h' : '-'}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full ${statusClasses[record.status] || 'bg-gray-100 text-gray-600'}">${statusText}</span>
                        </td>
                        <td class="px-6 py-4 text-center">${record.location || '-'}</td>
                        <td class="px-6 py-4 text-center">
                            <button class="text-blue-500 hover:text-blue-700 mr-2 view-details-btn" data-index="${start + index}">
                                <i class="ti ti-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        historyRows.innerHTML = html;

        // Update pagination info
        document.getElementById('showing-from').textContent = start + 1;
        document.getElementById('showing-to').textContent = Math.min(end, data.length);
        document.getElementById('total-records').textContent = data.length;

        // Update pagination buttons
        document.getElementById('prev-btn').disabled = page === 1;
        document.getElementById('next-btn').disabled = end >= data.length;
    }

    // Show attendance details modal
    function showDetails(index) {
        const record = mockHistory[index];
        if (!record) return;

        let breaksHtml = '';
        if (record.breaks && record.breaks.length > 0) {
            breaksHtml = '<h4 class="font-medium mb-2">Breaks:</h4><ul class="space-y-1">';
            record.breaks.forEach(breakItem => {
                breaksHtml += `<li class="text-sm">• ${breakItem.start} - ${breakItem.end} (${breakItem.duration}h)</li>`;
            });
            breaksHtml += '</ul>';
        } else {
            breaksHtml = '<p class="text-sm text-gray-600">No breaks recorded</p>';
        }

        modalContent.innerHTML = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date</label>
                        <p class="text-sm">${record.date} (${record.day})</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <p class="text-sm">${record.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Check In</label>
                        <p class="text-sm">${record.checkIn || 'Not recorded'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Check Out</label>
                        <p class="text-sm">${record.checkOut || 'Not recorded'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Hours</label>
                        <p class="text-sm">${record.totalHours ? record.totalHours.toFixed(1) + ' hours' : 'N/A'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Location</label>
                        <p class="text-sm">${record.location || 'Not recorded'}</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Break Details</label>
                    ${breaksHtml}
                </div>
                ${record.notes ? `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <p class="text-sm">${record.notes}</p>
                    </div>
                ` : ''}
            </div>
        `;

        detailsModal.classList.remove('hidden');
    }

    // Filter data
    function filterData() {
        const fromDate = document.getElementById('from-date').value;
        const toDate = document.getElementById('to-date').value;
        const status = document.getElementById('status-filter').value;

        let filtered = mockHistory.filter(record => {
            const recordDate = new Date(record.date);
            const from = fromDate ? new Date(fromDate) : null;
            const to = toDate ? new Date(toDate) : null;

            if (from && recordDate < from) return false;
            if (to && recordDate > to) return false;
            if (status && record.status !== status) return false;

            return true;
        });

        currentPage = 1;
        calculateSummary(filtered);
        renderHistory(filtered, currentPage);
    }

    // Export data
    function exportData() {
        // In real implementation, this would call an API to generate and download a file
        alert('Export functionality will be implemented with backend API');
    }

    // Event listeners
    filterBtn.addEventListener('click', filterData);
    exportBtn.addEventListener('click', exportData);

    document.getElementById('prev-btn').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            filterData();
        }
    });

    document.getElementById('next-btn').addEventListener('click', () => {
        currentPage++;
        filterData();
    });

    closeModal.addEventListener('click', () => {
        detailsModal.classList.add('hidden');
    });

    // Delegate event for view details buttons
    historyRows.addEventListener('click', (e) => {
        if (e.target.closest('.view-details-btn')) {
            const index = e.target.closest('.view-details-btn').dataset.index;
            showDetails(parseInt(index));
        }
    });

    // Initialize
    filterData();
});
</script>

@endsection