@extends('layouts.app')
@section('title', 'Monthly Attendance Summary')
@section('content')

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">Monthly Attendance Summary</h1>
            <h5 class="text-xs text-gray-500">View attendance statistics and reports for the month</h5>
        </div>

        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="ti ti-printer mr-2"></i>
                Print Report
            </button>
            <a href="{{ route('dashboard.attendance.index') }}"
                class="bg-red-400 hover:bg-red-600 text-white px-4 py-2 rounded-md">
                <i class="ti ti-arrow-left mr-2"></i>
                Back to Attendance
            </a>
        </div>
    </div>

    {{-- Month Selector --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-600">Select Month:</label>
            <input type="month" id="report-month" value="{{ date('Y-m') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400">
            <button id="generate-report-btn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                <i class="ti ti-chart-bar mr-2"></i>
                Generate Report
            </button>
        </div>
    </div>

    {{-- Monthly Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Total Working Days</h3>
                    <p class="text-3xl font-bold text-blue-600" id="total-working-days">25</p>
                </div>
                <i class="ti ti-calendar text-4xl text-blue-500"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Avg Attendance</h3>
                    <p class="text-3xl font-bold text-green-600" id="avg-attendance">85%</p>
                </div>
                <i class="ti ti-trending-up text-4xl text-green-500"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Total Absences</h3>
                    <p class="text-3xl font-bold text-red-600" id="total-absences">45</p>
                </div>
                <i class="ti ti-user-x text-4xl text-red-500"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Overtime Hours</h3>
                    <p class="text-3xl font-bold text-yellow-600" id="overtime-hours">120</p>
                </div>
                <i class="ti ti-clock text-4xl text-yellow-500"></i>
            </div>
        </div>
    </div>

    {{-- Staff-wise Summary Table --}}
    <div class="bg-white shadow-md sm:rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Staff-wise Attendance Summary</h3>
        <div class="overflow-x-auto">
            <table class="table bg-white table-bordered w-full">
                <thead class="text-xs text-gray-700 uppercase bg-gray-800 text-white">
                    <tr class="text-center">
                        <th class="px-6 py-3">Staff Member</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Present Days</th>
                        <th class="px-6 py-3">Absent Days</th>
                        <th class="px-6 py-3">Late Days</th>
                        <th class="px-6 py-3">Attendance %</th>
                        <th class="px-6 py-3">Total Hours</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody id="monthly-summary-rows">
                    {{-- Sample data - replace with dynamic data --}}
                    <tr class="bg-white border-b text-center">
                        <td class="px-6 py-4 flex items-center justify-center">
                            <img class="w-8 h-8 p-1 rounded-full ring-1 ring-gray-300 mr-2"
                                 src="https://avatar.iran.liara.run/public" alt="Avatar">
                            <span>John Doe</span>
                        </td>
                        <td class="px-6 py-4">Tailor</td>
                        <td class="px-6 py-4">22</td>
                        <td class="px-6 py-4">3</td>
                        <td class="px-6 py-4">1</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-600">88%</span>
                        </td>
                        <td class="px-6 py-4">176</td>
                        <td class="px-6 py-4">
                            <button class="text-blue-500 hover:text-blue-700">
                                <i class="ti ti-eye"></i> View Details
                            </button>
                        </td>
                    </tr>
                    <tr class="bg-white border-b text-center">
                        <td class="px-6 py-4 flex items-center justify-center">
                            <img class="w-8 h-8 p-1 rounded-full ring-1 ring-gray-300 mr-2"
                                 src="https://avatar.iran.liara.run/public" alt="Avatar">
                            <span>Jane Smith</span>
                        </td>
                        <td class="px-6 py-4">Master</td>
                        <td class="px-6 py-4">24</td>
                        <td class="px-6 py-4">1</td>
                        <td class="px-6 py-4">0</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-600">96%</span>
                        </td>
                        <td class="px-6 py-4">192</td>
                        <td class="px-6 py-4">
                            <button class="text-blue-500 hover:text-blue-700">
                                <i class="ti ti-eye"></i> View Details
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Monthly Calendar View --}}
    <div class="bg-white shadow-md sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Monthly Calendar View</h3>
        <div class="grid grid-cols-7 gap-2" id="calendar-grid">
            {{-- Calendar days will be generated here --}}
            <div class="text-center font-semibold text-gray-600 p-2">Sun</div>
            <div class="text-center font-semibold text-gray-600 p-2">Mon</div>
            <div class="text-center font-semibold text-gray-600 p-2">Tue</div>
            <div class="text-center font-semibold text-gray-600 p-2">Wed</div>
            <div class="text-center font-semibold text-gray-600 p-2">Thu</div>
            <div class="text-center font-semibold text-gray-600 p-2">Fri</div>
            <div class="text-center font-semibold text-gray-600 p-2">Sat</div>

            {{-- Calendar days (sample) --}}
            @for($i = 1; $i <= 31; $i++)
                <div class="border border-gray-200 p-4 text-center hover:bg-gray-50 cursor-pointer
                           {{ $i <= 25 ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-400' }}">
                    {{ $i }}
                    @if($i <= 25)
                        <div class="text-xs mt-1">
                            <i class="ti ti-check text-green-500"></i>
                        </div>
                    @endif
                </div>
            @endfor
        </div>
    </div>
</div>

{{-- JavaScript for monthly report --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reportMonth = document.getElementById('report-month');
    const generateBtn = document.getElementById('generate-report-btn');

    generateBtn.addEventListener('click', function() {
        const month = reportMonth.value;
        // Simulate report generation
        this.innerHTML = '<i class="ti ti-loader animate-spin mr-2"></i> Generating...';
        this.disabled = true;

        setTimeout(() => {
            // Update stats with mock data
            document.getElementById('total-working-days').textContent = '26';
            document.getElementById('avg-attendance').textContent = '82%';
            document.getElementById('total-absences').textContent = '52';
            document.getElementById('overtime-hours').textContent = '145';

            this.innerHTML = '<i class="ti ti-chart-bar mr-2"></i> Generate Report';
            this.disabled = false;

            // Show success message
            showToast('Monthly report generated successfully!', 'success');
        }, 2000);
    });
});

function showToast(message, type = 'info') {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} shadow-lg fixed top-4 right-4 z-50 max-w-sm`;
    toast.innerHTML = `
        <div>
            <i class="ti ti-info-circle"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>

@endsection