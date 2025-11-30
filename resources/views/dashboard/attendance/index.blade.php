@extends('layouts.app')
@section('title', 'Attendance Management')
@section('content')

<div class="container mx-auto py-2 px-2">
    <div class="flex items-center justify-between mb-6">
        {{-- Header Title --}}
        <div>
            <h1 class="text-2xl font-semibold">Attendance Management</h1>
            <h5 class="text-xs text-gray-500">Mark and manage staff attendance</h5>
        </div>

        <div class="flex space-x-2">
            <button class="bg-green-400 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                <i class="ti ti-plus mr-2"></i>
                Mark Attendance
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white p-4 pt-3 rounded-lg shadow">
            <h2 class="text-lg font-medium mb-1">Present Today</h2>
            <div class="flex items-center justify-between mt-4">
                <p class="text-2xl font-medium text-green-400">15</p>
                <i class="ti ti-user-check text-3xl text-green-400 px-4 py-2 bg-green-50 rounded-lg"></i>
            </div>
        </div>

        <div class="bg-white p-4 pt-3 rounded-lg shadow">
            <h2 class="text-lg font-medium mb-1">Absent Today</h2>
            <div class="flex items-center justify-between mt-4">
                <p class="text-2xl font-medium text-red-400">3</p>
                <i class="ti ti-user-x text-3xl text-red-400 px-4 py-2 bg-red-50 rounded-lg"></i>
            </div>
        </div>

        <div class="bg-white p-4 pt-3 rounded-lg shadow">
            <h2 class="text-lg font-medium mb-1">Late Arrivals</h2>
            <div class="flex items-center justify-between mt-4">
                <p class="text-2xl font-medium text-yellow-400">2</p>
                <i class="ti ti-clock text-3xl text-yellow-400 px-4 py-2 bg-yellow-50 rounded-lg"></i>
            </div>
        </div>

        <div class="bg-white p-4 pt-3 rounded-lg shadow">
            <h2 class="text-lg font-medium mb-1">Total Staff</h2>
            <div class="flex items-center justify-between mt-4">
                <p class="text-2xl font-medium text-blue-400">18</p>
                <i class="ti ti-users text-3xl text-blue-400 px-4 py-2 bg-blue-50 rounded-lg"></i>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white flex flex-wrap items-center justify-between gap-4 p-4 rounded-lg shadow mb-6">
        <!-- Date Picker -->
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600">Date:</label>
            <input type="date" id="attendance-date"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-indigo-400">
        </div>

        <!-- Role Filter -->
        <div>
            <select id="role-filter"
                class="border border-gray-300 py-2 px-3 rounded-md text-gray-700 focus:outline-none focus:ring focus:border-indigo-400">
                <option value="all">All Roles</option>
                <option value="tailor">Tailor</option>
                <option value="master">Master</option>
                <option value="stitcher">Stitcher</option>
            </select>
        </div>

        <!-- Search -->
        <div class="relative flex-1 min-w-[200px]">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" placeholder="Search staff..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-indigo-400">
        </div>

        <!-- Bulk Actions -->
        <div class="flex gap-2">
            <button class="bg-green-500 text-white px-3 py-2 rounded-lg hover:bg-green-600">
                Mark All Present
            </button>
            <button class="bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600">
                Mark All Absent
            </button>
        </div>
    </div>

    {{-- Attendance Table --}}
    <div class="bg-white shadow-md sm:rounded-lg p-2">
        <table id="attendance-table" class="table bg-white table-bordered w-full">
            <thead class="text-xs text-gray-700 uppercase bg-gray-800 text-white">
                <tr class="text-center">
                    <th class="px-6 py-3">Staff</th>
                    <th class="px-6 py-3">Role</th>
                    <th class="px-6 py-3">Check In</th>
                    <th class="px-6 py-3">Check Out</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Hours</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Sample data - replace with dynamic data --}}
                <tr class="bg-white border-b text-center">
                    <td class="px-6 py-4 flex items-center">
                        <img class="w-10 h-10 p-1 rounded-full ring-1 ring-gray-300 mr-2"
                             src="https://avatar.iran.liara.run/public" alt="Avatar">
                        <span>John Doe</span>
                    </td>
                    <td class="px-6 py-4">Tailor</td>
                    <td class="px-6 py-4">09:00 AM</td>
                    <td class="px-6 py-4">05:30 PM</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-600">Present</span>
                    </td>
                    <td class="px-6 py-4">8.5</td>
                    <td class="px-6 py-4">
                        <button class="text-blue-500 hover:text-blue-700 mr-2">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="text-red-500 hover:text-red-700">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr class="bg-white border-b text-center">
                    <td class="px-6 py-4 flex items-center">
                        <img class="w-10 h-10 p-1 rounded-full ring-1 ring-gray-300 mr-2"
                             src="https://avatar.iran.liara.run/public" alt="Avatar">
                        <span>Jane Smith</span>
                    </td>
                    <td class="px-6 py-4">Master</td>
                    <td class="px-6 py-4">-</td>
                    <td class="px-6 py-4">-</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-600">Absent</span>
                    </td>
                    <td class="px-6 py-4">0</td>
                    <td class="px-6 py-4">
                        <button class="text-blue-500 hover:text-blue-700 mr-2">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="text-red-500 hover:text-red-700">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection