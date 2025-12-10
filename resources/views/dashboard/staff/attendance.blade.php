@extends('layouts.app')
@section('title', 'My Attendance')
@section('content')

<div class="container mx-auto px-2 py-2">
    @if (session('success'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-2 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold">My Attendance</h1>
            <h5 class="text-xs text-gray-500">Track your daily attendance and working hours</h5>
        </div>
    </div>

    <!-- Today's Attendance Card -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-medium mb-4">Today's Attendance</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold {{ $todayAttendance ? 'text-green-600' : 'text-gray-600' }}">
                    {{ $todayAttendance ? 'Present' : 'Not Marked' }}
                </div>
                <p class="text-sm text-gray-600">Status</p>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">
                    {{ $todayAttendance && $todayAttendance->check_in_time ? \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') : '--:--' }}
                </div>
                <p class="text-sm text-gray-600">Check-in Time</p>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">
                    {{ $todayAttendance && $todayAttendance->working_hours ? $todayAttendance->working_hours . 'h' : '--' }}
                </div>
                <p class="text-sm text-gray-600">Working Hours</p>
            </div>
        </div>

        <!-- Check-in/Check-out Buttons -->
        <div class="mt-6 flex justify-center gap-4">
            @if(!$todayAttendance || !$todayAttendance->check_in_time)
                <form method="POST" action="{{ route('dashboard.staff.attendance') }}">
                    @csrf
                    <input type="hidden" name="action" value="check_in">
                    <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                        <i class="ti ti-login mr-2"></i>Check In
                    </button>
                </form>
            @else
                @if(!$todayAttendance->check_out_time)
                    <form method="POST" action="{{ route('dashboard.staff.attendance') }}">
                        @csrf
                        <input type="hidden" name="action" value="check_out">
                        <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600">
                            <i class="ti ti-logout mr-2"></i>Check Out
                        </button>
                    </form>
                @else
                    <p class="text-green-600 font-medium">Attendance completed for today</p>
                @endif
            @endif
        </div>
    </div>

    <!-- Attendance History -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium mb-4">Attendance History</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Check-in</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Check-out</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Working Hours</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($attendance->date)->format('M j, Y') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $attendance->status === 'present' ? 'bg-green-100 text-green-600' :
                                       ($attendance->status === 'absent' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600') }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '--:--' }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '--:--' }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $attendance->working_hours ? $attendance->working_hours . 'h' : '--' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                No attendance records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($attendances->hasPages())
            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection